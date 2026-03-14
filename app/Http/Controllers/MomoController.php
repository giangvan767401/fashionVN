<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
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
            ? Cart::where('user_id', Auth::id())->first()
            : Cart::where('session_id', session()->getId())->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $cartItems  = $cart->items()->with(['variant.product.images', 'variant.attributeValues.group'])->get();
        $subtotal   = $cartItems->sum(fn($i) => $i->unit_price * $i->quantity);
        $tax        = $subtotal * 0.08;
        $shippingFee = session('checkout.shipping_fee', 0);
        $total      = $subtotal + $tax + $shippingFee;
        $info       = session('checkout.info');

        // ----- 2. Tạo đơn hàng với trạng thái chờ thanh toán -----
        $order = DB::transaction(function () use ($cartItems, $subtotal, $tax, $shippingFee, $total, $info) {
            $order = Order::create([
                'order_number'   => 'ORD-' . strtoupper(uniqid()),
                'user_id'        => Auth::id(),
                'ship_name'      => $info['last_name'] . ' ' . $info['first_name'],
                'ship_phone'     => $info['phone'],
                'ship_province'  => $info['province'] ?? '',
                'ship_district'  => '',
                'ship_ward'      => '',
                'ship_address'   => $info['address'] . (!empty($info['apartment']) ? ' (' . $info['apartment'] . ')' : ''),
                'subtotal'       => $subtotal,
                'shipping_fee'   => $shippingFee,
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
            session()->forget(['checkout.info', 'checkout.shipping_method', 'checkout.shipping_fee']);

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
                session()->forget(['checkout.info', 'checkout.shipping_method', 'checkout.shipping_fee']);

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

        return view('momo.demo', compact('order', 'amount'));
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

        $order->update(['payment_status' => 'paid']);

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

            $order->update(['payment_status' => 'paid']);

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
