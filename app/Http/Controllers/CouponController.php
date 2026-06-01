<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    /**
     * Áp dụng mã giảm giá vào giỏ hàng.
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = strtoupper(trim($request->input('code')));

        // 1. Tìm coupon
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return back()->with('error', 'Mã giảm giá không tồn tại hoặc đã nhập sai.');
        }

        // 2. Tìm giỏ hàng hiện tại
        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
        } else {
            $cart = Cart::where('session_id', session()->getId())->first();
        }

        if (!$cart || $cart->items()->count() === 0) {
            return back()->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        // Tính tạm tính (subtotal)
        $cartItems = $cart->items()->get();
        $subtotal = $cartItems->sum(fn($item) => $item->unit_price * $item->quantity);

        // 3. Xác minh điều kiện áp dụng
        if (!$coupon->isValidFor($subtotal)) {
            if (!$coupon->is_active) {
                return back()->with('error', 'Mã giảm giá này hiện đã ngưng kích hoạt.');
            }
            if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
                return back()->with('error', 'Chương trình khuyến mãi này chưa bắt đầu.');
            }
            if ($coupon->expires_at && $coupon->expires_at->isPast()) {
                return back()->with('error', 'Mã giảm giá này đã hết hạn sử dụng.');
            }
            if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
                return back()->with('error', 'Mã giảm giá này đã hết số lần sử dụng.');
            }
            if ($subtotal < $coupon->min_order_amount) {
                return back()->with('error', 'Chưa đạt giá trị đơn hàng tối thiểu là ' . number_format($coupon->min_order_amount, 0, ',', '.') . 'đ.');
            }
            return back()->with('error', 'Mã giảm giá không hợp lệ cho đơn hàng hiện tại.');
        }

        // 4. Lưu vào giỏ hàng
        $cart->coupon_id = $coupon->id;
        $cart->save();

        return back()->with('status', 'Áp dụng mã giảm giá thành công.');
    }

    /**
     * Hủy áp dụng mã giảm giá.
     */
    public function remove()
    {
        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
        } else {
            $cart = Cart::where('session_id', session()->getId())->first();
        }

        if ($cart) {
            $cart->coupon_id = null;
            $cart->save();
        }

        return back()->with('status', 'Đã hủy mã giảm giá.');
    }
}
