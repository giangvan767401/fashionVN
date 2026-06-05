<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VNPayController extends Controller
{
    /**
     * Tạo đơn hàng và chuyển hướng sang cổng VNPAY.
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

        $cartItems = $cart->items()->with(['variant.product.images', 'variant.attributeValues.group'])->get();
        $subtotal  = $cartItems->sum(fn($i) => $i->unit_price * $i->quantity);

        // Tính giảm giá
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

        // Hạng thành viên & điểm
        $tierDiscount   = 0;
        $tierPercent    = 0;
        $pointsDiscount = 0;
        $pointsRedeemed = 0;
        $user = Auth::user();

        if ($user) {
            $tierPercent = $user->getTierDiscountPercent();
            if ($tierPercent > 0) {
                $tierDiscount = $subtotal * ($tierPercent / 100);
            }
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
        $tax         = $subtotalAfterDiscount * 0.08;
        $shippingFee = session('checkout.shipping_fee', 0);
        $total       = $subtotalAfterDiscount + $tax + $shippingFee;
        $info        = session('checkout.info');

        // ----- 2. Tạo đơn hàng nháp -----
        $order = DB::transaction(function () use ($cartItems, $subtotal, $discountAmount, $tax, $shippingFee, $total, $info, $coupon, $pointsRedeemed, $pointsDiscount, $tierDiscount) {
            $order = Order::create([
                'order_number'    => 'ORD-' . strtoupper(uniqid()),
                'user_id'         => Auth::id(),
                'coupon_id'       => $coupon ? $coupon->id : null,
                'payment_method_id' => 3, // VNPAY
                'ship_name'       => $info['last_name'] . ' ' . $info['first_name'],
                'ship_phone'      => $info['phone'],
                'ship_province'   => $info['province'] ?? '',
                'ship_district'   => '',
                'ship_ward'       => '',
                'ship_address'    => $info['address'] . (!empty($info['apartment']) ? ' (' . $info['apartment'] . ')' : ''),
                'subtotal'        => $subtotal,
                'shipping_fee'    => $shippingFee,
                'discount_amount' => $discountAmount,
                'points_redeemed' => $pointsRedeemed,
                'points_discount' => $pointsDiscount,
                'tier_discount'   => $tierDiscount,
                'tax_amount'      => $tax,
                'total_amount'    => $total,
                'status'          => 'pending',
                'payment_status'  => 'unpaid',
            ]);

            foreach ($cartItems as $item) {
                $product      = $item->variant->product;
                $primaryImage = $product?->images->where('is_primary', true)->first() ?? $product?->images->first();
                $imageUrl     = $primaryImage ? $primaryImage->url : null;

                \App\Models\OrderItem::create([
                    'order_id'      => $order->id,
                    'variant_id'    => $item->variant_id,
                    'product_name'  => $product?->name ?? 'Unknown',
                    'variant_label' => $item->variant_label,
                    'quantity'      => $item->quantity,
                    'unit_price'    => $item->unit_price,
                    'total_price'   => $item->unit_price * $item->quantity,
                    'image_url'     => $imageUrl,
                ]);

                $item->variant->decrement('quantity', $item->quantity);
            }

            return $order;
        });

        // Xoá giỏ hàng & session checkout
        $cart->items()->delete();
        $cart->delete();
        session()->forget(['checkout.info', 'checkout.shipping_method', 'checkout.shipping_fee', 'checkout.use_points']);

        // ----- 3. Build VNPAY Payment URL -----
        $tmnCode    = env('VNPAY_TMN_CODE', 'DEMOV210');
        $hashSecret = env('VNPAY_HASH_SECRET', 'RAOEXHYVSDDIIENL');
        $vnpUrl     = env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $returnUrl  = env('VNPAY_RETURN_URL', url('/vnpay/return'));

        $amount     = (int) round($order->total_amount) * 100; // VNPAY requires * 100
        $txnRef     = $order->order_number;
        $createDate = now()->format('YmdHis');
        $expireDate = now()->addMinutes(15)->format('YmdHis');
        $ipAddr     = $request->ip() ?: '127.0.0.1';
        $orderInfo  = 'Thanh toan don hang ' . $order->order_number;
        $locale     = 'vn';
        $currCode   = 'VND';

        $vnpData = [
            'vnp_Version'    => '2.1.0',
            'vnp_Command'    => 'pay',
            'vnp_TmnCode'    => $tmnCode,
            'vnp_Amount'     => $amount,
            'vnp_CreateDate' => $createDate,
            'vnp_CurrCode'   => $currCode,
            'vnp_ExpireDate' => $expireDate,
            'vnp_IpAddr'     => $ipAddr,
            'vnp_Locale'     => $locale,
            'vnp_OrderInfo'  => $orderInfo,
            'vnp_OrderType'  => 'other',
            'vnp_ReturnUrl'  => $returnUrl,
            'vnp_TxnRef'     => $txnRef,
        ];

        ksort($vnpData);
        $queryString = http_build_query($vnpData, '', '&', PHP_QUERY_RFC3986);
        $vnpSecureHash = hash_hmac('sha512', $queryString, $hashSecret);
        $paymentUrl = $vnpUrl . '?' . $queryString . '&vnp_SecureHash=' . $vnpSecureHash;

        // Demo mode: redirect sang trang giả lập thay vì VNPAY thật
        $isDemoMode = env('VNPAY_DEMO_MODE', true);
        if ($isDemoMode) {
            return redirect()->route('vnpay.demo', $order->id);
        }

        return redirect()->away($paymentUrl);
    }

    /**
     * Trang giả lập thanh toán VNPAY (demo mode).
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
        $confirmScanUrl = "http://{$host}" . ($port ? ":{$port}" : "") . "/vnpay/confirm-scan/" . $order->id;

        return view('vnpay.demo', compact('order', 'confirmScanUrl'));
    }

    /**
     * Xử lý return URL từ VNPAY sau khi thanh toán.
     */
    public function returnPayment(Request $request)
    {
        $hashSecret    = env('VNPAY_HASH_SECRET', 'RAOEXHYVSDDIIENL');
        $vnpSecureHash = $request->input('vnp_SecureHash');
        $txnRef        = $request->input('vnp_TxnRef');
        $responseCode  = $request->input('vnp_ResponseCode');

        // Xác minh chữ ký
        $inputData = $request->except(['vnp_SecureHash', 'vnp_SecureHashType']);
        ksort($inputData);
        $queryString    = http_build_query($inputData, '', '&', PHP_QUERY_RFC3986);
        $calculatedHash = hash_hmac('sha512', $queryString, $hashSecret);

        if ($calculatedHash !== $vnpSecureHash) {
            return redirect()->route('home')->with('error', 'Chữ ký VNPAY không hợp lệ.');
        }

        // Tìm đơn hàng
        $order = Order::where('order_number', $txnRef)->first();
        if (!$order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }

        // Kiểm tra mã kết quả – '00' là thành công
        if ($responseCode === '00') {
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
                    'gateway'          => 'vnpay',
                    'transaction_code' => $request->input('vnp_TransactionNo', 'VNP-' . strtoupper(uniqid())),
                    'amount'           => $order->total_amount,
                    'currency'         => 'VND',
                    'status'           => 'success',
                    'gateway_response' => $request->all(),
                    'paid_at'          => now(),
                ]);
            }
            return redirect()->route('checkout.success', $order->id);
        }

        // Thanh toán thất bại / bị huỷ
        return redirect()->route('home')->with('error', 'Giao dịch VNPAY không thành công hoặc bị hủy (mã: ' . $responseCode . ').');
    }

    /**
     * Hủy đơn hàng và rollback.
     */
    public function cancelPayment(Request $request, $orderId)
    {
        $order = Order::with('items.variant')->find($orderId);
        if ($order) {
            $this->rollbackOrder($order);
        }
        return redirect()->route('cart.index')->with('error', 'Bạn đã hủy giao dịch thanh toán VNPAY.');
    }

    /**
     * Xác nhận thanh toán demo từ trang web.
     */
    public function demoConfirm(Request $request)
    {
        $orderId = $request->input('order_id');
        $order   = Order::find($orderId);

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
            \App\Models\PaymentTransaction::create([
                'order_id'         => $order->id,
                'gateway'          => 'vnpay',
                'transaction_code' => 'DEMO-' . strtoupper(uniqid()),
                'amount'           => $order->total_amount,
                'currency'         => 'VND',
                'status'           => 'success',
                'gateway_response' => ['mode' => 'demo', 'resultCode' => 0],
                'paid_at'          => now(),
            ]);
        }

        return redirect()->route('checkout.success', $order->id);
    }

    /**
     * Hủy từ trang demo.
     */
    public function demoCancel(Request $request)
    {
        $orderId = $request->input('order_id');
        $order   = Order::with('items.variant')->find($orderId);
        if ($order) {
            $this->rollbackOrder($order);
        }
        return redirect()->route('cart.index')->with('error', 'Bạn đã hủy giao dịch thanh toán VNPAY.');
    }

    /**
     * Hiển thị giao diện thanh toán trên điện thoại khi quét QR giả lập.
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
            <title>Thanh toán VNPAY</title>
            <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
            <style>
                body {
                    font-family: "Nunito", sans-serif;
                    background: #eff6ff;
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
                    box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.1);
                    padding: 24px;
                    border: 1px solid #bfdbfe;
                }
                .header { text-align: center; margin-bottom: 24px; }
                .logo { font-size: 24px; font-weight: 800; color: #005baa; margin-bottom: 4px; }
                .subtitle { font-size: 13px; color: #64748b; }
                .amount-card {
                    background: #eff6ff;
                    border: 1px solid #bfdbfe;
                    border-radius: 12px;
                    padding: 16px;
                    text-align: center;
                    margin-bottom: 20px;
                }
                .amount-label { font-size: 12px; color: #1e40af; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }
                .amount-value { font-size: 28px; font-weight: 800; color: #1e3a8a; }
                .info-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 12px 0;
                    border-bottom: 1px solid #e0f2fe;
                    font-size: 14px;
                }
                .info-label { color: #64748b; font-weight: 500; }
                .info-value { color: #0f172a; font-weight: 700; }
                .btn-submit {
                    display: block;
                    width: 100%;
                    padding: 16px;
                    background: #005baa;
                    color: #fff;
                    font-size: 15px;
                    font-weight: 800;
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                    margin-top: 24px;
                    box-shadow: 0 4px 6px -1px rgba(0, 91, 170, 0.25);
                }
                .btn-submit:hover { background: #004a8f; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <div class="logo">VN<span style="color:#e31837;">PAY</span></div>
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
                    <span class="info-value">Cổng VNPAY</span>
                </div>
                <form method="POST" action="' . route('vnpay.execute_confirm_scan', $order->id) . '">
                    ' . csrf_field() . '
                    <button type="submit" class="btn-submit">✓ &nbsp; XÁC NHẬN THANH TOÁN</button>
                </form>
            </div>
        </body>
        </html>
        ';
    }

    /**
     * Thực thi xác nhận từ điện thoại.
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
                'gateway'          => 'vnpay',
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
                    background: #eff6ff;
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
                    box-shadow: 0 4px 20px rgba(0, 91, 170, 0.12);
                    max-width: 400px;
                    width: 100%;
                }
                .icon { font-size: 64px; color: #005baa; margin-bottom: 20px; }
                h1 { font-size: 22px; color: #333; margin-bottom: 12px; }
                p { font-size: 14px; color: #666; line-height: 1.6; margin-bottom: 24px; }
            </style>
        </head>
        <body>
            <div class="card">
                <div class="icon">✓</div>
                <h1>Thanh toán thành công!</h1>
                <p>Mã đơn hàng: <strong>' . htmlspecialchars($order->order_number) . '</strong><br>Đơn hàng đã được xác nhận thanh toán qua VNPAY. Bạn có thể đóng trình duyệt này và quay lại màn hình máy tính.</p>
                <div style="font-size: 12px; color: #005baa; font-weight: bold; margin-top: 10px;">Lumiere cảm ơn bạn!</div>
            </div>
        </body>
        </html>
        ';
    }

    /**
     * Hoàn tác đơn hàng (rollback kho, xóa đơn).
     */
    private function rollbackOrder(Order $order)
    {
        if ($order->payment_status === 'unpaid') {
            foreach ($order->items as $item) {
                if ($item->variant) {
                    $item->variant->increment('quantity', $item->quantity);
                }
            }
            if ($order->coupon) {
                $order->coupon->decrement('used_count');
            }
            $order->items()->delete();
            $order->delete();
        }
    }
}
