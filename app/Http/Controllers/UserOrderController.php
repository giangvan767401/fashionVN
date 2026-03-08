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

        // Đánh dấu đã đọc thông báo liên quan đến đơn hàng này
        \App\Models\Notification::where('user_id', Auth::id())
            ->where('action_url', '/profile/orders/' . $id)
            ->where('is_read', 0)
            ->update(['is_read' => 1, 'read_at' => now()]);

        return view('profile.order-detail', compact('order'));
    }

    public function confirmReceived($id, Request $request)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if ($order->status !== 'completed') {
            return back()->with('error', 'Chỉ có thể xác nhận khi đơn hàng đã được giao.');
        }

        $action = $request->input('action');

        if ($action === 'received') {
            $order->update(['status' => 'finished']);
            return back()->with('success', 'Cảm ơn bạn đã xác nhận! Đơn hàng đã được hoàn thành.');
        } elseif ($action === 'not_received') {
            $order->update(['status' => 'delivery_failed']);
            return back()->with('error', 'Đã ghi nhận phản hồi. Chúng tôi sẽ liên hệ hỗ trợ bạn sớm nhất.');
        }

        return back()->with('error', 'Hành động không hợp lệ.');
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
