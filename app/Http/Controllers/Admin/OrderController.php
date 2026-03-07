<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items.variant.product'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,shipped,completed,cancelled,delivery_failed'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return back()->with('success', 'Trạng thái đơn hàng đã được cập nhật.');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        if (!in_array($order->status, ['completed', 'cancelled', 'delivery_failed'])) {
            return back()->with('error', 'Chỉ có thể xóa đơn hàng đã hoàn thành, đã hủy hoặc giao không thành công.');
        }

        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Đơn hàng đã được xóa khỏi hệ thống.');
    }
}
