<x-admin-layout>
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Quản lý đơn hàng</h1>
            <p class="text-gray-500 mt-1">Xem và cập nhật trạng thái các đơn hàng trong hệ thống.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-2xl flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-sm font-bold text-gray-400 uppercase tracking-wider">ID / Mã đơn</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-400 uppercase tracking-wider">Người mua</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-400 uppercase tracking-wider">Tổng tiền</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-400 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-400 uppercase tracking-wider text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($orders as $order)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">#{{ $order->id }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $order->order_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $order->ship_name }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $order->ship_phone }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                                        'shipped' => 'bg-sky-50 text-sky-600 border-sky-100',
                                        'completed' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                        'cancelled' => 'bg-rose-50 text-rose-600 border-rose-100',
                                        'delivery_failed' => 'bg-rose-50 text-rose-600 border-rose-100',
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Chờ xử lý',
                                        'shipped' => 'Đang giao',
                                        'completed' => 'Đã giao',
                                        'cancelled' => 'Đã hủy',
                                        'delivery_failed' => 'Giao hàng thất bại',
                                    ];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $statusClasses[$order->status] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-900 text-white rounded-xl text-xs font-bold hover:bg-gray-800 transition-colors">
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
