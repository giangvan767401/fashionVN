<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Coupon;
use App\Http\Controllers\MomoController;
use App\Http\Controllers\VNPayController;

class CheckoutController extends Controller
{
    /**
     * Lấy toàn bộ dữ liệu tạm tính, tiền thuế, phí vận chuyển và giảm giá khuyến mãi.
     */
    private function getCheckoutData()
    {
        $cartItems = collect();
        $cartTotal = 0; // Tạm tính ban đầu (subtotal)
        $discountAmount = 0;
        $coupon = null;
        $cart = null;

        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->with('coupon')->first();
        } else {
            $cart = Cart::where('session_id', session()->getId())->with('coupon')->first();
        }

        if ($cart) {
            $cartItems = $cart->items()->with(['variant.product.images', 'variant.attributeValues.group'])->get();
            $cartTotal = $cartItems->sum(fn($item) => $item->unit_price * $item->quantity);
            
            // Xác thực và tính toán mã giảm giá
            if ($cart->coupon) {
                if ($cart->coupon->isValidFor($cartTotal)) {
                    $coupon = $cart->coupon;
                    $discountAmount = $coupon->calculateDiscount($cartTotal);
                } else {
                    // Mã giảm giá không còn hợp lệ -> Tự động gỡ
                    $cart->coupon_id = null;
                    $cart->save();
                    session()->flash('warning', 'Mã giảm giá đã được gỡ bỏ do đơn hàng của bạn không còn đủ điều kiện.');
                }
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
                $tierDiscount = $cartTotal * ($tierPercent / 100);
            }

            // 2. Sử dụng điểm tích lũy (nếu khách chọn)
            if (session('checkout.use_points', false)) {
                // Tạm tính còn lại sau khi trừ voucher và giảm hạng thành viên
                $remainingSubtotal = max(0, $cartTotal - $discountAmount - $tierDiscount);
                
                // Quy đổi: 1 điểm = 100đ
                $maxRedeemablePoints = min($user->loyalty_points, (int) floor($remainingSubtotal / 100));
                if ($maxRedeemablePoints > 0) {
                    $pointsRedeemed = $maxRedeemablePoints;
                    $pointsDiscount = $pointsRedeemed * 100;
                }
            }
        }

        $subtotalAfterDiscount = max(0, $cartTotal - $discountAmount - $tierDiscount - $pointsDiscount);
        $tax = $subtotalAfterDiscount * 0.08;
        $shippingFee = session('checkout.shipping_fee', 0);
        $total = $subtotalAfterDiscount + $tax + $shippingFee;

        return [
            'cart' => $cart,
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'coupon' => $coupon,
            'discountAmount' => $discountAmount,
            'tierPercent' => $tierPercent,
            'tierDiscount' => $tierDiscount,
            'pointsRedeemed' => $pointsRedeemed,
            'pointsDiscount' => $pointsDiscount,
            'subtotalAfterDiscount' => $subtotalAfterDiscount,
            'tax' => $tax,
            'shippingFee' => $shippingFee,
            'total' => $total,
        ];
    }

    public function info()
    {
        $checkoutData = $this->getCheckoutData();
        $cartItems = $checkoutData['cartItems'];
        $cartTotal = $checkoutData['cartTotal'];
        $coupon = $checkoutData['coupon'];
        $discountAmount = $checkoutData['discountAmount'];
        $tierPercent = $checkoutData['tierPercent'];
        $tierDiscount = $checkoutData['tierDiscount'];
        $pointsRedeemed = $checkoutData['pointsRedeemed'];
        $pointsDiscount = $checkoutData['pointsDiscount'];
        $tax = $checkoutData['tax'];
        $total = $checkoutData['total'];

        $user = Auth::user();
        $savedAddress = null;
        if ($user) {
            $savedAddress = \Illuminate\Support\Facades\DB::table('user_addresses')
                ->where('user_id', $user->id)
                ->orderByDesc('id')
                ->first();
        }

        return view('checkout.info', compact(
            'cartItems', 'cartTotal', 'coupon', 'discountAmount', 
            'tierPercent', 'tierDiscount', 'pointsRedeemed', 'pointsDiscount',
            'tax', 'total', 'user', 'savedAddress'
        ));
    }

    public function storeInfo(Request $request)
    {
        // Simple validation
        $request->validate([
            'email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'phone' => 'required',
        ]);

        // If user logged in and checked "Save Info"
        if (Auth::check() && $request->has('save_info')) {
            \Illuminate\Support\Facades\DB::table('user_addresses')->insert([
                'user_id' => Auth::id(),
                'label' => 'Mặc định',
                'recipient_name' => $request->last_name . ' ' . $request->first_name,
                'recipient_phone' => $request->phone,
                'province' => $request->province ?? '',
                'district' => '', // Not in form
                'ward' => '', // Not in form
                'street_address' => $request->address . ($request->apartment ? ' (' . $request->apartment . ')' : ''),
                'postal_code' => $request->postal_code,
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Save shipping info to session
        session()->put('checkout.info', $request->except('_token'));

        return redirect()->route('checkout.shipping');
    }

    public function shipping()
    {
        if (!session()->has('checkout.info')) {
            return redirect()->route('checkout');
        }

        $checkoutData = $this->getCheckoutData();
        $cartItems = $checkoutData['cartItems'];
        $cartTotal = $checkoutData['cartTotal'];
        $coupon = $checkoutData['coupon'];
        $discountAmount = $checkoutData['discountAmount'];
        $tierPercent = $checkoutData['tierPercent'];
        $tierDiscount = $checkoutData['tierDiscount'];
        $pointsRedeemed = $checkoutData['pointsRedeemed'];
        $pointsDiscount = $checkoutData['pointsDiscount'];
        $tax = $checkoutData['tax'];
        $total = $checkoutData['total'];
        $shippingFee = $checkoutData['shippingFee'];

        $info = session('checkout.info');
        $user = Auth::user();

        return view('checkout.shipping', compact(
            'cartItems', 'cartTotal', 'coupon', 'discountAmount', 
            'tierPercent', 'tierDiscount', 'pointsRedeemed', 'pointsDiscount',
            'tax', 'total', 'info', 'shippingFee', 'user'
        ));
    }

    public function storeShipping(Request $request)
    {
        $request->validate([
            'shipping_method' => 'required|in:standard,express',
            'delivery_date' => 'nullable', // added field
        ]);

        session()->put('checkout.shipping_method', $request->shipping_method);

        $fee = 0;
        if (str_starts_with($request->delivery_date, 'express_1')) $fee = 25000;
        if (str_starts_with($request->delivery_date, 'express_2')) $fee = 24000;
        session()->put('checkout.shipping_fee', $fee);

        return redirect()->route('checkout.payment');
    }

    public function payment()
    {
        if (!session()->has('checkout.info') || !session()->has('checkout.shipping_method')) {
            return redirect()->route('checkout.shipping');
        }

        $checkoutData = $this->getCheckoutData();
        $cartItems = $checkoutData['cartItems'];
        $cartTotal = $checkoutData['cartTotal'];
        $coupon = $checkoutData['coupon'];
        $discountAmount = $checkoutData['discountAmount'];
        $tierPercent = $checkoutData['tierPercent'];
        $tierDiscount = $checkoutData['tierDiscount'];
        $pointsRedeemed = $checkoutData['pointsRedeemed'];
        $pointsDiscount = $checkoutData['pointsDiscount'];
        $tax = $checkoutData['tax'];
        $total = $checkoutData['total'];
        $shippingFee = $checkoutData['shippingFee'];

        $info = session('checkout.info');
        $shippingMethod = session('checkout.shipping_method');
        $user = Auth::user();

        return view('checkout.payment', compact(
            'cartItems', 'cartTotal', 'coupon', 'discountAmount', 
            'tierPercent', 'tierDiscount', 'pointsRedeemed', 'pointsDiscount',
            'tax', 'total', 'info', 'shippingMethod', 'shippingFee', 'user'
        ));
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cod,momo,vietqr,vnpay',
        ]);

        // Chuyển hướng sang luồng thanh toán MoMo
        if ($request->payment_method === 'momo') {
            return app(MomoController::class)->createPayment($request);
        }

        // Chuyển hướng sang luồng thanh toán VietQR (PayOS)
        if ($request->payment_method === 'vietqr') {
            return app(PayOSController::class)->createPayment($request);
        }

        // Chuyển hướng sang luồng thanh toán VNPAY
        if ($request->payment_method === 'vnpay') {
            return app(VNPayController::class)->createPayment($request);
        }

        if (!session()->has('checkout.info')) {
            return redirect()->route('checkout');
        }

        // 1. Get Checkout data
        $checkoutData = $this->getCheckoutData();
        $cart = $checkoutData['cart'];
        $cartItems = $checkoutData['cartItems'];
        $subtotal = $checkoutData['cartTotal'];
        $coupon = $checkoutData['coupon'];
        $discountAmount = $checkoutData['discountAmount'];
        $tierDiscount = $checkoutData['tierDiscount'];
        $pointsRedeemed = $checkoutData['pointsRedeemed'];
        $pointsDiscount = $checkoutData['pointsDiscount'];
        $tax = $checkoutData['tax'];
        $shippingFee = $checkoutData['shippingFee'];
        $total = $checkoutData['total'];

        if (!$cart || $cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $info = session('checkout.info');

        // 2. Create Order
        $order = \App\Models\Order::create([
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'user_id' => Auth::id(),
            'coupon_id' => $coupon ? $coupon->id : null,
            'payment_method_id' => 1,
            'ship_name' => $info['last_name'] . ' ' . $info['first_name'],
            'ship_phone' => $info['phone'],
            'ship_province' => $info['province'] ?? '',
            'ship_district' => '',
            'ship_ward' => '',
            'ship_address' => $info['address'] . ($info['apartment'] ? ' (' . $info['apartment'] . ')' : ''),
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'discount_amount' => $discountAmount,
            'points_redeemed' => $pointsRedeemed,
            'points_discount' => $pointsDiscount,
            'tier_discount' => $tierDiscount,
            'tax_amount' => $tax,
            'total_amount' => $total,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        // 3. Create Order Items and Update Stock
        foreach ($cartItems as $item) {
            $product = $item->variant->product;
            $primaryImage = $product?->images->where('is_primary', true)->first() ?? $product?->images->first();
            $imageUrl = $primaryImage ? $primaryImage->url : null;

            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'variant_id' => $item->variant_id,
                'product_name' => $product?->name ?? 'Unknown',
                'variant_label' => $item->variant_label,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->unit_price * $item->quantity,
                'image_url' => $imageUrl,
            ]);

            // 4. Subtract stock quantity
            $item->variant->decrement('quantity', $item->quantity);
        }

        // Trừ điểm tích lũy của khách hàng nếu đã sử dụng
        if ($pointsRedeemed > 0 && $order->user) {
            $order->user->decrement('loyalty_points', $pointsRedeemed);
        }

        // Increase coupon usage limit
        if ($coupon) {
            $coupon->increment('used_count');
        }

        // 5. Clear Cart
        if ($cart) {
            $cart->items()->delete();
            $cart->delete();
        }

        // 5. Clear Session
        session()->forget(['checkout.info', 'checkout.shipping_method', 'checkout.shipping_fee', 'checkout.use_points']);

        // 6. Redirect to Success
        return redirect()->route('checkout.success', $order->id);
    }

    public function success($id)
    {
        $order = \App\Models\Order::with('items')->where('user_id', Auth::id())->findOrFail($id);
        return view('checkout.success', compact('order'));
    }
}
