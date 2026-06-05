<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayOSController extends Controller
{
    /**
     * Tạo đơn hàng tạm thời và chuyển hướng tới cổng thanh toán PayOS.
     */
    public function createPayment(Request $request)
    {
        if (!session()->has('checkout.info') || !session()->has('checkout.shipping_method')) {
            return redirect()->route('checkout');
        }

        // ----- 1. Lấy cấu hình API keys -----
        $clientId = config('services.payos.client_id');
        $apiKey = config('services.payos.api_key');
        $checksumKey = config('services.payos.checksum_key');
        $isDemoMode = env('PAYOS_DEMO_MODE', true);

        // Bật demo nếu thiếu key hoặc cài đặt trong .env
        $useDemo = $isDemoMode || !$clientId || !$apiKey || !$checksumKey;

        // ----- 2. Lấy giỏ hàng -----
        $cart = Auth::check()
            ? Cart::where('user_id', Auth::id())->with('coupon')->first()
            : Cart::where('session_id', session()->getId())->with('coupon')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $cartItems  = $cart->items()->with(['variant.product.images', 'variant.attributeValues.group'])->get();
        $subtotal   = $cartItems->sum(fn($i) => $i->unit_price * $i->quantity);
        
        // Tính toán mã giảm giá
        $discountAmount = 0;
        $coupon = null;
        if ($cart->coupon) {
            if ($cart->coupon->isValidFor($subtotal)) {
                $coupon = $cart->coupon;
                $discountAmount = $coupon->calculateDiscount($subtotal);
            } else {
                $cart->coupon_id = null;
                $cart->save();
            }
        }

        // TÍNH TOÁN HẠNG THÀNH VIÊN VÀ ĐIỂM
        $tierDiscount = 0;
        $tierPercent = 0;
        $pointsDiscount = 0;
        $pointsRedeemed = 0;
        $user = Auth::user();

        if ($user) {
            // Giảm giá theo hạng thành viên (áp dụng trên subtotal gốc)
            $tierPercent = $user->getTierDiscountPercent();
            if ($tierPercent > 0) {
                $tierDiscount = $subtotal * ($tierPercent / 100);
            }

            // Sử dụng điểm tích lũy
            if (session('checkout.use_points', false)) {
                $remainingSubtotal = max(0, $subtotal - $discountAmount - $tierDiscount);
                $maxRedeemablePoints = min($user->loyalty_points, (int) floor($remainingSubtotal / 100));
                if ($maxRedeemablePoints > 0) {
                    $pointsRedeemed = $maxRedeemablePoints;
                    $pointsDiscount = $pointsRedeemed * 100;
                }
            }
        }

        $subtotalAfterDiscount = max(0, $subtotal - $discountAmount - $tierDiscount - $pointsDiscount);
        $tax        = $subtotalAfterDiscount * 0.08;
        $shippingFee = session('checkout.shipping_fee', 0);
        $total      = $subtotalAfterDiscount + $tax + $shippingFee;
        $info       = session('checkout.info');

        // ----- 3. Tạo đơn hàng nháp -----
        $order = DB::transaction(function () use ($cartItems, $subtotal, $discountAmount, $tax, $shippingFee, $total, $info, $coupon, $pointsRedeemed, $pointsDiscount, $tierDiscount) {
            $order = Order::create([
                'order_number'   => 'ORD-' . strtoupper(uniqid()),
                'user_id'        => Auth::id(),
                'coupon_id'      => $coupon ? $coupon->id : null,
                'payment_method_id' => 2,
                'ship_name'      => $info['last_name'] . ' ' . $info['first_name'],
                'ship_phone'     => $info['phone'],
                'ship_province'  => $info['province'] ?? '',
                'ship_district'  => '',
                'ship_ward'      => '',
                'ship_address'   => $info['address'] . (!empty($info['apartment']) ? ' (' . $info['apartment'] . ')' : ''),
                'subtotal'       => $subtotal,
                'shipping_fee'   => $shippingFee,
                'discount_amount' => $discountAmount,
                'points_redeemed' => $pointsRedeemed,
                'points_discount' => $pointsDiscount,
                'tier_discount'   => $tierDiscount,
                'tax_amount'     => $tax,
                'total_amount'   => $total,
                'status'         => 'pending',
                'payment_status' => 'unpaid',
            ]);

            foreach ($cartItems as $item) {
                $product      = $item->variant->product;
                $primaryImage = $product?->images->where('is_primary', true)->first() ?? $product?->images->first();
                $imageUrl     = $primaryImage ? $primaryImage->url : null;

                \App\Models\OrderItem::create([
                    'order_id'     => $order->id,
                    'variant_id'   => $item->variant_id,
                    'product_name' => $product?->name ?? 'Unknown',
                    'variant_label' => $item->variant_label,
                    'quantity'     => $item->quantity,
                    'unit_price'   => $item->unit_price,
                    'total_price'  => $item->unit_price * $item->quantity,
                    'image_url'    => $imageUrl,
                ]);

                $item->variant->decrement('quantity', $item->quantity);
            }

            return $order;
        });

        // ----- 4. Tạo liên kết thanh toán và gửi tới PayOS -----
        if ($useDemo) {
            // Xoá giỏ hàng và session checkout
            $cart->items()->delete();
            $cart->delete();
            session()->forget(['checkout.info', 'checkout.shipping_method', 'checkout.shipping_fee', 'checkout.use_points']);

            return redirect()->route('payos.demo', $order->id);
        }

        $description = "Lumiere don " . $order->id;

        $data = [
            'orderCode' => $order->id,
            'amount' => (int) round($order->total_amount),
            'description' => substr($description, 0, 25),
            'cancelUrl' => route('payos.cancel', $order->id),
            'returnUrl' => route('payos.return', $order->id),
        ];

        // Sắp xếp Alphabetical các khoá và mã hoá
        ksort($data);
        $parts = [];
        foreach ($data as $key => $value) {
            $parts[] = "{$key}={$value}";
        }
        $signingString = implode('&', $parts);
        $signature = hash_hmac('sha256', $signingString, $checksumKey);

        $data['signature'] = $signature;

        try {
            $response = Http::withHeaders([
                'x-client-id' => $clientId,
                'x-api-key' => $apiKey,
            ])->timeout(30)->post('https://api-merchant.payos.vn/v2/payment-requests', $data);

            $result = $response->json();

            if (isset($result['code']) && $result['code'] === '00' && isset($result['data']['checkoutUrl'])) {
                // Xoá giỏ hàng và session checkout sau khi liên kết được tạo thành công
                $cart->items()->delete();
                $cart->delete();

                session()->forget(['checkout.info', 'checkout.shipping_method', 'checkout.shipping_fee', 'checkout.use_points']);

                return redirect()->away($result['data']['checkoutUrl']);
            }

            // PayOS báo lỗi -> huỷ order tạm thời để hoàn kho
            $this->cancelPayment($request, $order->id);
            Log::error('PayOS create payment link error', ['response' => $result]);
            return redirect()->route('checkout.payment')->with('error', 'Lỗi kết nối PayOS: ' . ($result['desc'] ?? 'Không xác định.'));

        } catch (\Exception $e) {
            $this->cancelPayment($request, $order->id);
            Log::error('PayOS connection exception', ['message' => $e->getMessage()]);
            return redirect()->route('checkout.payment')->with('error', 'Không thể kết nối tới cổng thanh toán PayOS. Vui lòng thử lại.');
        }
    }

    /**
     * Webhook nhận thông báo thanh toán thành công từ PayOS (Server-to-Server).
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();
        Log::info('PayOS Webhook IPN received', $payload);

        $checksumKey = config('services.payos.checksum_key');

        if (!isset($payload['data']) || !isset($payload['signature'])) {
            return response()->json(['message' => 'Invalid webhook payload'], 400);
        }

        $data = $payload['data'];
        $receivedSignature = $payload['signature'];

        // Sắp xếp Alphabetical các khoá bên trong data và verify chữ ký
        ksort($data);
        $parts = [];
        foreach ($data as $key => $value) {
            if ($key === 'signature') continue;
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            $parts[] = "{$key}={$value}";
        }
        $signingString = implode('&', $parts);
        $expectedSignature = hash_hmac('sha256', $signingString, $checksumKey);

        if (!hash_equals($expectedSignature, $receivedSignature)) {
            Log::warning('PayOS Webhook signature mismatch', [
                'expected' => $expectedSignature,
                'received' => $receivedSignature
            ]);
            return response()->json(['message' => 'Signature mismatch'], 400);
        }

        // Cập nhật trạng thái đơn hàng
        $orderId = $data['orderCode'];
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($order->payment_status !== 'paid') {
            $order->update(['payment_status' => 'paid']);
            if ($order->coupon) {
                $order->coupon->increment('used_count');
            }
            if ($order->points_redeemed > 0 && $order->user) {
                $order->user->decrement('loyalty_points', $order->points_redeemed);
            }

            \App\Models\PaymentTransaction::create([
                'order_id'         => $order->id,
                'gateway'          => 'payos',
                'transaction_code' => $data['reference'] ?? 'PAYOS-' . strtoupper(uniqid()),
                'amount'           => $order->total_amount,
                'currency'         => 'VND',
                'status'           => 'success',
                'gateway_response' => $payload,
                'paid_at'          => now(),
            ]);
        }

        return response()->json(['message' => 'OK'], 200);
    }

    /**
     * Quay lại từ PayOS khi thanh toán thành công.
     */
    public function returnPayment(Request $request, $orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }

        $status = $request->query('status');
        if ($status === 'PAID') {
            if ($order->payment_status !== 'paid') {
                $order->update(['payment_status' => 'paid']);
                if ($order->coupon) {
                    $order->coupon->increment('used_count');
                }
                if ($order->points_redeemed > 0 && $order->user) {
                    $order->user->decrement('loyalty_points', $order->points_redeemed);
                }

                \App\Models\PaymentTransaction::firstOrCreate(
                    ['order_id' => $order->id, 'gateway' => 'payos'],
                    [
                        'transaction_code' => $request->query('id') ?? 'PAYOS-' . strtoupper(uniqid()),
                        'amount'           => $order->total_amount,
                        'currency'         => 'VND',
                        'status'           => 'success',
                        'gateway_response' => $request->all(),
                        'paid_at'          => now(),
                    ]
                );
            }
            return redirect()->route('checkout.success', $order->id)->with('success', 'Thanh toán qua PayOS (VietQR) thành công!');
        }

        return redirect()->route('checkout.success', $order->id)->with('warning', 'Đơn hàng đang chờ hoàn tất xử lý giao dịch.');
    }

    /**
     * Huỷ bỏ thanh toán PayOS, hoàn kho và xoá đơn nháp.
     */
    public function cancelPayment(Request $request, $orderId)
    {
        $order = Order::with('items.variant')->find($orderId);
        if ($order) {
            DB::transaction(function () use ($order) {
                foreach ($order->items as $item) {
                    if ($item->variant) {
                        $item->variant->increment('quantity', $item->quantity);
                    }
                }
                $order->items()->delete();
                $order->delete();
            });
        }

        return redirect()->route('cart.index')->with('error', 'Giao dịch chuyển khoản PayOS đã bị huỷ.');
    }

    /**
     * Trang giả lập VietQR (chỉ dùng khi demo_mode = true hoặc thiếu keys).
     */
    public function demo($orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }

        $host = request()->getHost();
        $port = request()->getPort();
        if (in_array($host, ['127.0.0.1', 'localhost'])) {
            $localIp = gethostbyname(gethostname());
            if ($localIp && $localIp !== '127.0.0.1' && $localIp !== '127.0.0.2') {
                $host = $localIp;
            }
        }
        $confirmScanUrl = "http://{$host}" . ($port ? ":{$port}" : "") . "/payos/confirm-scan/" . $order->id;

        $bankId = env('VIETQR_BANK_ID', 'MB');
        $accountNo = env('VIETQR_ACCOUNT_NO', '0963123456');
        $accountName = env('VIETQR_ACCOUNT_NAME', 'NGUYEN VAN A');
        $amount = (int) round($order->total_amount);
        $addInfo = $order->order_number;

        $vietQrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.png?amount={$amount}&addInfo=" . urlencode($addInfo) . "&accountName=" . urlencode($accountName);

        return view('payos.demo', compact('order', 'confirmScanUrl', 'vietQrUrl', 'bankId', 'accountNo', 'accountName'));
    }

    /**
     * Xác nhận thanh toán qua QR (giao diện điện thoại hiển thị thông tin chuyển khoản và giá tiền).
     */
    public function confirmScan($orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return response('Không tìm thấy đơn hàng.', 404);
        }

        return '
        <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Thanh toán đơn hàng</title>
            <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
            <style>
                body {
                    font-family: "Nunito", sans-serif;
                    background: #f1f5f9;
                    margin: 0;
                    padding: 16px;
                    display: flex;
                    justify-content: center;
                    align-items: flex-start;
                    min-height: 100vh;
                    box-sizing: border-box;
                }
                .container {
                    background: #ffffff;
                    width: 100%;
                    max-width: 420px;
                    border-radius: 16px;
                    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
                    padding: 24px;
                    border: 1px solid #e2e8f0;
                }
                .header {
                    text-align: center;
                    margin-bottom: 24px;
                }
                .logo {
                    font-size: 24px;
                    font-weight: 800;
                    color: #2563eb;
                    margin-bottom: 4px;
                }
                .subtitle {
                    font-size: 13px;
                    color: #64748b;
                }
                .amount-card {
                    background: #eff6ff;
                    border: 1px solid #bfdbfe;
                    border-radius: 12px;
                    padding: 16px;
                    text-align: center;
                    margin-bottom: 20px;
                }
                .amount-label {
                    font-size: 12px;
                    color: #1e40af;
                    font-weight: 700;
                    text-transform: uppercase;
                    margin-bottom: 4px;
                }
                .amount-value {
                    font-size: 28px;
                    font-weight: 800;
                    color: #1e3a8a;
                }
                .info-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 12px 0;
                    border-bottom: 1px solid #f1f5f9;
                    font-size: 14px;
                }
                .info-label {
                    color: #64748b;
                    font-weight: 500;
                }
                .info-value {
                    color: #0f172a;
                    font-weight: 700;
                }
                .btn-submit {
                    display: block;
                    width: 100%;
                    padding: 16px;
                    background: #2563eb;
                    color: #fff;
                    font-size: 15px;
                    font-weight: 800;
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                    margin-top: 24px;
                    text-align: center;
                    box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
                    text-decoration: none;
                }
                .btn-submit:hover {
                    background: #1d4ed8;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <div class="logo">Viet<span style="color:#0f172a;">QR</span></div>
                    <div class="subtitle">Cổng thanh toán giả lập di động</div>
                </div>
                
                <div class="amount-card">
                    <div class="amount-label">Số tiền cần thanh toán</div>
                    <div class="amount-value">' . number_format($order->total_amount, 0, ',', '.') . 'đ</div>
                </div>

                <div class="info-row">
                    <span class="info-label">Ngân hàng thụ hưởng</span>
                    <span class="info-value">MB Bank (Ngân hàng Quân đội)</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Số tài khoản</span>
                    <span class="info-value">0963123456</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tên chủ tài khoản</span>
                    <span class="info-value">NGUYEN VAN A</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nội dung chuyển khoản</span>
                    <span class="info-value" style="color: #2563eb;">' . htmlspecialchars($order->order_number) . '</span>
                </div>

                <form method="POST" action="' . route('payos.execute_confirm_scan', $order->id) . '">
                    ' . csrf_field() . '
                    <button type="submit" class="btn-submit">
                        ✓ &nbsp; XÁC NHẬN ĐÃ CHUYỂN TIỀN
                    </button>
                </form>
            </div>
        </body>
        </html>
        ';
    }

    /**
     * Thực thi cập nhật dữ liệu khi khách xác nhận đã chuyển tiền trên điện thoại.
     */
    public function executeConfirmScan($orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return response('Không tìm thấy đơn hàng.', 404);
        }

        if ($order->payment_status !== 'paid') {
            $order->update(['payment_status' => 'paid']);
            if ($order->coupon) {
                $order->coupon->increment('used_count');
            }
            if ($order->points_redeemed > 0 && $order->user) {
                $order->user->decrement('loyalty_points', $order->points_redeemed);
            }

            \App\Models\PaymentTransaction::create([
                'order_id'         => $order->id,
                'gateway'          => 'payos',
                'transaction_code' => 'SCAN-' . strtoupper(uniqid()),
                'amount'           => $order->total_amount,
                'currency'         => 'VND',
                'status'           => 'success',
                'gateway_response' => ['mode' => 'scan_demo_execute', 'resultCode' => 0],
                'paid_at'          => now(),
            ]);
        }

        return '
        <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Thanh toán thành công</title>
            <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
            <style>
                body {
                    font-family: "Nunito", sans-serif;
                    background: #f4f6f9;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    margin: 0;
                    padding: 20px;
                    box-sizing: border-box;
                }
                .card {
                    background: #fff;
                    border-radius: 16px;
                    padding: 40px 30px;
                    text-align: center;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
                    max-width: 400px;
                    width: 100%;
                }
                .icon {
                    font-size: 64px;
                    color: #2e7d32;
                    margin-bottom: 20px;
                }
                h1 {
                    font-size: 22px;
                    color: #333;
                    margin-bottom: 12px;
                }
                p {
                    font-size: 14px;
                    color: #666;
                    line-height: 1.6;
                    margin-bottom: 24px;
                }
            </style>
        </head>
        <body>
            <div class="card">
                <div class="icon">✓</div>
                <h1>Thanh toán thành công!</h1>
                <p>Đơn hàng đã được xác nhận thanh toán thành công. Bạn có thể đóng trình duyệt này và quay lại màn hình máy tính.</p>
                <div style="font-size: 12px; color: #4a5845; font-weight: bold; margin-top: 10px;">Lumiere cảm ơn bạn!</div>
            </div>
        </body>
        </html>
        ';
    }
}
