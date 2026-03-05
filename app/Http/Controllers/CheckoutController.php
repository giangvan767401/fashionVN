<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;

class CheckoutController extends Controller
{
    public function info()
    {
        $cartItems = collect();
        $cartTotal = 0;

        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
        } else {
            $cart = Cart::where('session_id', session()->getId())->first();
        }

        if ($cart) {
            $cartItems = $cart->items()->with(['variant.product.images', 'variant.attributeValues.group'])->get();
            $cartTotal = $cartItems->sum(fn($item) => $item->unit_price * $item->quantity);
        }

        $user = Auth::user();
        $savedAddress = null;
        if ($user) {
            $savedAddress = \Illuminate\Support\Facades\DB::table('user_addresses')
                ->where('user_id', $user->id)
                ->orderByDesc('id')
                ->first();
        }

        return view('checkout.info', compact('cartItems', 'cartTotal', 'user', 'savedAddress'));
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

        $cartItems = collect();
        $cartTotal = 0;

        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
        } else {
            $cart = Cart::where('session_id', session()->getId())->first();
        }

        if ($cart) {
            $cartItems = $cart->items()->with(['variant.product.images', 'variant.attributeValues.group'])->get();
            $cartTotal = $cartItems->sum(fn($item) => $item->unit_price * $item->quantity);
        }

        $info = session('checkout.info');

        return view('checkout.shipping', compact('cartItems', 'cartTotal', 'info'));
    }

    public function storeShipping(Request $request)
    {
        $request->validate([
            'shipping_method' => 'required|in:standard,express',
            'delivery_date' => 'nullable', // added field
        ]);

        session()->put('checkout.shipping_method', $request->shipping_method);
        
        $fee = 0;
        if (str_starts_with($request->delivery_date, 'express_1')) $fee = 25;
        if (str_starts_with($request->delivery_date, 'express_2')) $fee = 24;
        session()->put('checkout.shipping_fee', $fee);

        return redirect()->route('checkout.payment');
    }

    public function payment()
    {
        if (!session()->has('checkout.info') || !session()->has('checkout.shipping_method')) {
            return redirect()->route('checkout.shipping');
        }

        $cartItems = collect();
        $cartTotal = 0;

        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
        } else {
            $cart = Cart::where('session_id', session()->getId())->first();
        }

        if ($cart) {
            $cartItems = $cart->items()->with(['variant.product.images', 'variant.attributeValues.group'])->get();
            $cartTotal = $cartItems->sum(fn($item) => $item->unit_price * $item->quantity);
        }

        $info = session('checkout.info');
        $shippingMethod = session('checkout.shipping_method');
        $shippingFee = session('checkout.shipping_fee', 0);

        return view('checkout.payment', compact('cartItems', 'cartTotal', 'info', 'shippingMethod', 'shippingFee'));
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cod',
        ]);

        if (!session()->has('checkout.info')) {
            return redirect()->route('checkout');
        }

        // 1. Get Cart
        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
        } else {
            $cart = Cart::where('session_id', session()->getId())->first();
        }

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $cartItems = $cart->items()->with(['variant.product.images', 'variant.attributeValues.group'])->get();
        $subtotal = $cartItems->sum(fn($item) => $item->unit_price * $item->quantity);
        $tax = $subtotal * 0.08;
        $shippingFee = session('checkout.shipping_fee', 0);
        $total = $subtotal + $tax + $shippingFee;

        $info = session('checkout.info');

        // 2. Create Order
        $order = \App\Models\Order::create([
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'user_id' => Auth::id(),
            'ship_name' => $info['last_name'] . ' ' . $info['first_name'],
            'ship_phone' => $info['phone'],
            'ship_province' => $info['province'] ?? '',
            'ship_district' => '',
            'ship_ward' => '',
            'ship_address' => $info['address'] . ($info['apartment'] ? ' (' . $info['apartment'] . ')' : ''),
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'tax_amount' => $tax,
            'total_amount' => $total,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        // 3. Create Order Items
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
        }

        // 4. Clear Cart
        $cart->items()->delete();
        $cart->delete();

        // 5. Clear Session
        session()->forget(['checkout.info', 'checkout.shipping_method', 'checkout.shipping_fee']);

        // 6. Redirect to Success
        return redirect()->route('checkout.success', $order->id);
    }

    public function success($id)
    {
        $order = \App\Models\Order::with('items')->where('user_id', Auth::id())->findOrFail($id);
        return view('checkout.success', compact('order'));
    }
}
