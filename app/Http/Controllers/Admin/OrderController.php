<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->input('per_page', 10);
        $orders = $query->paginate($perPage)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function export(Request $request)
    {
        $query = Order::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->get();

        $filename = "don_hang_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Ma Don', 'Nguoi Mua', 'SDT', 'Phuong Thuc TT', 'So Luong SP', 'Tong Tien', 'Trang Thai', 'Thoi Gian'];

        $callback = function() use($orders, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM cho UTF-8 Excel
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                $statusLabels = [
                    'pending' => 'Cho xu ly',
                    'shipped' => 'Dang giao',
                    'completed' => 'Da giao',
                    'cancelled' => 'Da huy',
                    'delivery_failed' => 'Giao hang that bai',
                ];

                $row['ID']  = $order->id;
                $row['Ma Don'] = $order->order_number;
                $row['Nguoi Mua'] = $order->ship_name;
                $row['SDT'] = $order->ship_phone;
                $row['Phuong Thuc TT'] = 'COD';
                $row['So Luong SP'] = $order->items->sum('quantity') ?? (count($order->items) > 0 ? count($order->items) : 1);
                $row['Tong Tien'] = $order->total_amount;
                $row['Trang Thai'] = $statusLabels[$order->status] ?? $order->status;
                $row['Thoi Gian'] = $order->created_at->format('d/m/Y H:i');

                fputcsv($file, [
                    $row['ID'], $row['Ma Don'], $row['Nguoi Mua'], $row['SDT'], 
                    $row['Phuong Thuc TT'], $row['So Luong SP'], $row['Tong Tien'], 
                    $row['Trang Thai'], $row['Thoi Gian']
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items.variant.product'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function print($id)
    {
        $order = Order::with(['user', 'items.variant.product'])->findOrFail($id);
        return view('admin.orders.print', compact('order'));
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
