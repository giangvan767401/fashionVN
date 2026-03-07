<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserOrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('profile.orders', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('items')
            ->where('user_id', Auth::id())
            ->findOrFail($id);
        return view('profile.order-detail', compact('order'));
    }

    public function cancel($id)
    {
        $order = Order::with('items.variant')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if ($order->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể hủy đơn hàng khi đang ở trạng thái chờ xử lý.');
        }

        \Illuminate\Support\Facades\DB::transaction(function() use ($order) {
            // Restore stock
            foreach ($order->items as $item) {
                if ($item->variant) {
                    $item->variant->increment('quantity', $item->quantity);
                }
            }

            $order->update(['status' => 'cancelled']);
        });

        return back()->with('success', 'Đơn hàng của bạn đã được hủy thành công.');
    }
}
