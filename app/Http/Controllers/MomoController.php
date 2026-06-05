<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MomoController extends Controller
{
    /**
     * Tạo đơn hàng tạm và chuyển hướng tới cổng MoMo.
     */
    public function createPayment(Request $request)
    {
        if (!session()->has('checkout.info') || !session()->has('checkout.shipping_method')) {
            return redirect()->route('checkout');
        }

        // ----- 1. Lấy giỏ hàng -----
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
            // 1. Giảm giá theo hạng thành viên (áp dụng trên subtotal gốc)
            $tierPercent = $user->getTierDiscountPercent();
            if ($tierPercent > 0) {
                $tierDiscount = $subtotal * ($tierPercent / 100);
            }

            // 2. Sử dụng điểm tích lũy (nếu khách chọn)
            if (session('checkout.use_points', false)) {
                // Tạm tính còn lại sau khi trừ voucher và giảm hạng thành viên
                $remainingSubtotal = max(0, $subtotal - $discountAmount - $tierDiscount);
                
                // Quy đổi: 1 điểm = 100đ
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

        // ----- 2. Tạo đơn hàng với trạng thái chờ thanh toán -----
        $order = DB::transaction(function () use ($cartItems, $subtotal, $discountAmount, $tax, $shippingFee, $total, $info, $coupon, $pointsRedeemed, $pointsDiscount, $tierDiscount) {
            $order = Order::create([
                'order_number'   => 'ORD-' . strtoupper(uniqid()),
                'user_id'        => Auth::id(),
                'coupon_id'      => $coupon ? $coupon->id : null,
                'payment_method_id' => 4,
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

        // Lưu order_id vào session để xác minh sau
        session()->put('momo_pending_order_id', $order->id);

        // ----- 3. Chế độ Demo: hiện trang giả lập MoMo -----
        if (config('services.momo.demo_mode', true)) {
            $cart->items()->delete();
            $cart->delete();
            session()->forget(['checkout.info', 'checkout.shipping_method', 'checkout.shipping_fee', 'checkout.use_points']);

            return redirect()->route('momo.demo', [
                'order_number' => $order->order_number,
                'amount'       => (int) round($total),
            ]);
        }

        // ----- 4. Gọi API MoMo (sandbox hoặc thật) -----
        $partnerCode = config('services.momo.partner_code');
        $accessKey   = config('services.momo.access_key');
        $secretKey   = config('services.momo.secret_key');
        $endpoint    = config('services.momo.endpoint');

        $returnUrl  = config('services.momo.return_url') ?: route('momo.return');
        $notifyUrl  = config('services.momo.notify_url') ?: route('momo.notify');

        $orderId    = $order->order_number;
        $requestId  = $partnerCode . '_' . time();
        $amount     = (int) round($total);
        $orderInfo  = 'Thanh toan don hang ' . $order->order_number;
        $requestType = 'captureWallet';
        $extraData  = '';

        $rawHash = "accessKey={$accessKey}&amount={$amount}&extraData={$extraData}"
            . "&ipnUrl={$notifyUrl}&orderId={$orderId}&orderInfo={$orderInfo}"
            . "&partnerCode={$partnerCode}&redirectUrl={$returnUrl}"
            . "&requestId={$requestId}&requestType={$requestType}";

        $signature = hash_hmac('sha256', $rawHash, $secretKey);

        $payload = [
            'partnerCode' => $partnerCode,
            'accessKey'   => $accessKey,
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $returnUrl,
            'ipnUrl'      => $notifyUrl,
            'lang'        => 'vi',
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature,
        ];

        try {
            $response = Http::timeout(30)->post($endpoint, $payload);
            $data     = $response->json();

            if (isset($data['payUrl']) && $data['resultCode'] === 0) {
                // Xóa giỏ hàng và session checkout
                $cart->items()->delete();
                $cart->delete();
                session()->forget(['checkout.info', 'checkout.shipping_method', 'checkout.shipping_fee', 'checkout.use_points']);

                return redirect()->away($data['payUrl']);
            }

            // MoMo trả về lỗi → hoàn kho và xóa đơn tạm
            $this->rollbackOrder($order);
            Log::error('MoMo createPayment error', ['response' => $data]);

            return redirect()->route('checkout.payment')->with('error', 'Không thể kết nối cổng thanh toán MoMo. Vui lòng thử lại.');
        } catch (\Exception $e) {
            $this->rollbackOrder($order);
            Log::error('MoMo exception', ['message' => $e->getMessage()]);

            return redirect()->route('checkout.payment')->with('error', 'Có lỗi xảy ra khi kết nối MoMo. Vui lòng thử lại.');
        }
    }

    /**
     * Trang giả lập MoMo (chỉ dùng khi demo_mode = true).
     */
    public function demo(\Illuminate\Http\Request $request)
    {
        $orderNumber = $request->query('order_number');
        $amount      = $request->query('amount');

        $order = Order::where('order_number', $orderNumber)->first();
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
        $confirmScanUrl = "http://{$host}" . ($port ? ":{$port}" : "") . "/momo/confirm-scan/" . $order->order_number;

        $momoPhone = env('MOMO_RECEIVER_PHONE');
        if (!$momoPhone) {
            $accNo = env('VIETQR_ACCOUNT_NO', '9116102005');
            $momoPhone = (strpos($accNo, '0') === 0) ? $accNo : '0' . $accNo;
        }
        $momoRealUrl = "https://nhantien.momo.vn/{$momoPhone}/" . (int)round($amount);

        return view('momo.demo', compact('order', 'amount', 'confirmScanUrl', 'momoRealUrl'));
    }

    /**
     * Hiển thị giao diện thanh toán MoMo khi quét mã QR từ điện thoại.
     */
    public function confirmScan($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->first();

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
                    background: #fdf2f8;
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
                    box-shadow: 0 10px 25px -5px rgba(219, 39, 119, 0.1), 0 8px 10px -6px rgba(219, 39, 119, 0.1);
                    padding: 24px;
                    border: 1px solid #fbcfe8;
                }
                .header {
                    text-align: center;
                    margin-bottom: 24px;
                }
                .logo {
                    font-size: 24px;
                    font-weight: 800;
                    color: #ae2070;
                    margin-bottom: 4px;
                }
                .subtitle {
                    font-size: 13px;
                    color: #64748b;
                }
                .amount-card {
                    background: #fdf2f8;
                    border: 1px solid #fbcfe8;
                    border-radius: 12px;
                    padding: 16px;
                    text-align: center;
                    margin-bottom: 20px;
                }
                .amount-label {
                    font-size: 12px;
                    color: #be185d;
                    font-weight: 700;
                    text-transform: uppercase;
                    margin-bottom: 4px;
                }
                .amount-value {
                    font-size: 28px;
                    font-weight: 800;
                    color: #9d174d;
                }
                .info-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 12px 0;
                    border-bottom: 1px solid #fce7f3;
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
                    background: #ae2070;
                    color: #fff;
                    font-size: 15px;
                    font-weight: 800;
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                    margin-top: 24px;
                    text-align: center;
                    box-shadow: 0 4px 6px -1px rgba(174, 32, 112, 0.2);
                    text-decoration: none;
                }
                .btn-submit:hover {
                    background: #8f1a5c;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <div class="logo">Ví <span style="color:#0f172a;">MoMo</span></div>
                    <div class="subtitle">Cổng thanh toán giả lập di động</div>
                </div>
                
                <div class="amount-card">
                    <div class="amount-label">Số tiền cần thanh toán</div>
                    <div class="amount-value">' . number_format($order->total_amount, 0, ',', '.') . 'đ</div>
                </div>

                <div class="info-row">
                    <span class="info-label">Nhà cung cấp</span>
                    <span class="info-value">LUMIERE WOMEN CLOTHING</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Mã đơn hàng</span>
                    <span class="info-value">' . htmlspecialchars($order->order_number) . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phương thức</span>
                    <span class="info-value">Ví điện tử MoMo</span>
                </div>

                <form method="POST" action="' . route('momo.execute_confirm_scan', $order->order_number) . '">
                    ' . csrf_field() . '
                    <button type="submit" class="btn-submit">
                        ✓ &nbsp; XÁC NHẬN THANH TOÁN
                    </button>
                </form>
            </div>
        </body>
        </html>
        ';
    }

    /**
     * Thực thi thanh toán và đánh dấu đơn hàng là đã thanh toán từ điện thoại.
     */
    public function executeConfirmScan($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->first();

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
                'gateway'          => 'momo',
                'transaction_code' => 'SCAN-' . strtoupper(uniqid()),
                'amount'           => $order->total_amount,
                'currency'         => 'VND',
                'status'           => 'success',
                'gateway_response' => ['mode' => 'scan', 'resultCode' => 0],
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
                    background: #white;
                    background-color: #fff;
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
                <p>Mã đơn hàng: <strong>' . htmlspecialchars($orderNumber) . '</strong><br>Đơn hàng đã được xác nhận thanh toán thành công. Bạn có thể đóng trình duyệt này và quay lại màn hình máy tính.</p>
                <div style="font-size: 12px; color: #ae2070; font-weight: bold; margin-top: 10px;">Lumiere cảm ơn bạn!</div>
            </div>
        </body>
        </html>
        ';
    }

    /**
     * Trả về trạng thái thanh toán của đơn hàng (dùng để polling từ JS).
     */
    public function checkStatus($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        return response()->json([
            'payment_status' => $order->payment_status
        ]);
    }

    /**
     * Hủy đơn hàng từ trang demo (hoàn kho, xóa đơn).
     */
    public function demoCancel(\Illuminate\Http\Request $request)
    {
        $orderNumber = $request->input('order_number');
        $order = Order::with('items.variant')->where('order_number', $orderNumber)->first();

        if ($order) {
            $this->rollbackOrder($order);
        }

        session()->forget('momo_pending_order_id');
        return redirect()->route('cart.index')->with('error', 'Bạn đã hủy giao dịch thanh toán MoMo.');
    }

    /**
     * Xác nhận thanh toán từ trang demo.
     */
    public function demoConfirm(\Illuminate\Http\Request $request)
    {
        $orderNumber = $request->input('order_number');
        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }

        if ($order->payment_status !== 'paid') {
            $order->update(['payment_status' => 'paid']);
            if ($order->coupon) {
                $order->coupon->increment('used_count');
            }
            if ($order->points_redeemed > 0 && $order->user) {
                $order->user->decrement('loyalty_points', $order->points_redeemed);
            }
        }

        \App\Models\PaymentTransaction::create([
            'order_id'         => $order->id,
            'gateway'          => 'momo',
            'transaction_code' => 'DEMO-' . strtoupper(uniqid()),
            'amount'           => $order->total_amount,
            'currency'         => 'VND',
            'status'           => 'success',
            'gateway_response' => ['mode' => 'demo', 'resultCode' => 0],
            'paid_at'          => now(),
        ]);

        session()->forget('momo_pending_order_id');
        return redirect()->route('checkout.success', $order->id)
            ->with('success', 'Thanh toán MoMo thành công!');
    }

    /**
     * Xử lý redirect từ MoMo thật (người dùng quay lại website).
     */
    public function returnPayment(Request $request)
    {
        $resultCode  = (int) $request->input('resultCode');
        $orderId     = $request->input('orderId');
        $message     = $request->input('message', '');
        $transId     = $request->input('transId', 'DEMO-' . strtoupper(uniqid()));
        $isDemoMode  = config('services.momo.demo_mode', true);

        $order = Order::where('order_number', $orderId)->first();

        if (!$order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }

        // Chế độ demo: bỏ qua xác minh chữ ký, luôn coi là thành công
        if ($isDemoMode || $resultCode === 0) {
            if (!$isDemoMode && !$this->verifyReturnSignature($request->all())) {
                return redirect()->route('home')->with('error', 'Chữ ký không hợp lệ.');
            }

            if ($order->payment_status !== 'paid') {
                $order->update(['payment_status' => 'paid']);
                if ($order->coupon) {
                    $order->coupon->increment('used_count');
                }
            }

            \App\Models\PaymentTransaction::create([
                'order_id'         => $order->id,
                'gateway'          => 'momo',
                'transaction_code' => (string) $transId,
                'amount'           => $order->total_amount,
                'currency'         => 'VND',
                'status'           => 'success',
                'gateway_response' => $isDemoMode ? array_merge($request->all(), ['demo_mode' => true]) : $request->all(),
                'paid_at'          => now(),
            ]);

            session()->forget('momo_pending_order_id');
            $msg = $isDemoMode ? '[DEMO] Thanh toán MoMo thành công!' : 'Thanh toán MoMo thành công!';
            return redirect()->route('checkout.success', $order->id)->with('success', $msg);
        }

        // Thanh toán thất bại / người dùng huỷ (chỉ áp dụng khi không phải demo)
        $this->rollbackOrder($order);
        session()->forget('momo_pending_order_id');

        return redirect()->route('checkout.payment')->with('error', 'Thanh toán MoMo thất bại: ' . $message);
    }

    /**
     * Xử lý IPN (server-to-server callback) từ MoMo.
     */
    public function notify(Request $request)
    {
        Log::info('MoMo IPN received', $request->all());

        if (!$this->verifyReturnSignature($request->all())) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $resultCode = (int) $request->input('resultCode');
        $orderId    = $request->input('orderId');
        $transId    = $request->input('transId', '');
        $amount     = $request->input('amount');

        $order = Order::where('order_number', $orderId)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($resultCode === 0 && $order->payment_status !== 'paid') {
            $order->update(['payment_status' => 'paid']);
            if ($order->coupon) {
                $order->coupon->increment('used_count');
            }

            \App\Models\PaymentTransaction::updateOrCreate(
                ['order_id' => $order->id, 'gateway' => 'momo'],
                [
                    'transaction_code' => (string) $transId,
                    'amount'           => $order->total_amount,
                    'currency'         => 'VND',
                    'status'           => 'success',
                    'gateway_response' => $request->all(),
                    'paid_at'          => now(),
                ]
            );
        }

        return response()->json(['message' => 'OK'], 200);
    }

    // ---------- helpers ----------

    private function verifyReturnSignature(array $params): bool
    {
        $accessKey  = config('services.momo.access_key');
        $secretKey  = config('services.momo.secret_key');
        $partnerCode = config('services.momo.partner_code');

        $received  = $params['signature'] ?? '';
        $returnUrl = config('services.momo.return_url') ?: route('momo.return');
        $notifyUrl = config('services.momo.notify_url') ?: route('momo.notify');

        // MoMo ký các trường sau theo thứ tự alphabetical trong response
        $rawHash = "accessKey={$accessKey}&amount={$params['amount']}&extraData={$params['extraData']}"
            . "&message={$params['message']}&orderId={$params['orderId']}&orderInfo={$params['orderInfo']}"
            . "&orderType={$params['orderType']}&partnerCode={$partnerCode}"
            . "&payType={$params['payType']}&requestId={$params['requestId']}&responseTime={$params['responseTime']}"
            . "&resultCode={$params['resultCode']}&transId={$params['transId']}";

        $expected = hash_hmac('sha256', $rawHash, $secretKey);

        return hash_equals($expected, $received);
    }

    private function rollbackOrder(Order $order): void
    {
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
}
