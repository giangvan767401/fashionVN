<x-admin-layout>
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('admin.orders.index') }}" class="p-2 bg-white border border-gray-100 rounded-2xl text-gray-400 hover:text-gray-900 hover:shadow-sm transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Chi tiết đơn hàng #{{ $order->id }}</h1>
            <p class="text-gray-500 mt-1">Mã đơn: <span class="font-medium">{{ $order->order_number }}</span> • Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 text-emerald-600 rounded-2xl border border-emerald-100 font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-rose-50 text-rose-600 rounded-2xl border border-rose-100 font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Items Table -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/30">
                    <h3 class="font-bold text-gray-900 uppercase text-sm tracking-wider">Sản phẩm trong đơn</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Sản phẩm</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase text-center">Số lượng</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase text-right">Đơn giá</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase text-right">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            @if($item->image_url)
                                                <img src="{{ Str::startsWith($item->image_url, 'http') ? $item->image_url : asset('storage/' . $item->image_url) }}" class="w-12 h-16 object-cover rounded-lg border border-gray-100" alt="">
                                            @else
                                                <div class="w-12 h-16 bg-gray-100 rounded-lg border border-gray-100 flex items-center justify-center text-gray-300">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-bold text-gray-900 line-clamp-1">{{ $item->product_name }}</div>
                                                <div class="text-xs text-gray-400 mt-0.5">{{ $item->variant_label }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-medium text-gray-600">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium text-gray-900">
                                        ${{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-900">
                                        ${{ number_format($item->total_price, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-6 bg-gray-50/30 border-t border-gray-100">
                    <div class="flex flex-col gap-2 max-w-xs ml-auto">
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Tạm tính:</span>
                            <span>${{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Phí ship:</span>
                            <span>${{ number_format($order->shipping_fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Thuế (8%):</span>
                            <span>${{ number_format($order->tax_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200">
                            <span class="font-bold text-gray-900">Tổng cộng:</span>
                            <span class="text-xl font-black text-gray-900">${{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Info -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/30">
                    <h3 class="font-bold text-gray-900 uppercase text-sm tracking-wider">Thông tin giao hàng</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Người nhận</p>
                        <p class="font-bold text-gray-900">{{ $order->ship_name }}</p>
                        <p class="text-gray-600 mt-1">{{ $order->ship_phone }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Địa chỉ</p>
                        <p class="text-gray-600 leading-relaxed">{{ $order->ship_address }}</p>
                        <p class="text-gray-600">{{ $order->ship_ward }}, {{ $order->ship_district }}, {{ $order->ship_province }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-8">
            <!-- Update Status Card -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-900 uppercase text-sm tracking-wider mb-6">Cập nhật trạng thái</h3>
                
                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')
                    
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 block">Trạng thái hiện tại</label>
                        <select name="status" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-4 focus:ring-gray-900/5 transition-all font-bold text-gray-900">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Đang giao</option>
                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Đã giao</option>
                            <option value="finished" {{ $order->status == 'finished' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="delivery_failed" {{ $order->status == 'delivery_failed' ? 'selected' : '' }}>Giao hàng thất bại</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-4 bg-gray-900 text-white rounded-2xl font-bold shadow-lg shadow-gray-200 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Lưu thay đổi
                    </button>
                </form>
            </div>

            <!-- Danger Zone -->
            @if(in_array($order->status, ['completed', 'cancelled', 'delivery_failed']))
                <div class="bg-rose-50 p-8 rounded-3xl border border-rose-100">
                    <h3 class="font-bold text-rose-900 uppercase text-xs tracking-wider mb-2">Danger Zone</h3>
                    <p class="text-rose-700 text-xs leading-relaxed mb-6">Dọn dẹp đơn hàng đã hoàn tất, đã hủy hoặc giao không thành công.</p>
                    
                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn đơn hàng này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-3 border-2 border-rose-200 text-rose-600 rounded-2xl font-bold hover:bg-rose-500 hover:text-white hover:border-rose-500 transition-all">
                            Xóa đơn hàng
                        </button>
                    </form>
                </div>
            @endif

            <!-- Customer Notes -->
            @if($order->customer_note)
                <div class="bg-amber-50 p-6 rounded-3xl border border-amber-100">
                    <h3 class="font-bold text-amber-900 uppercase text-xs tracking-wider mb-2">Ghi chú từ khách hàng</h3>
                    <p class="text-amber-800 text-sm leading-relaxed">{{ $order->customer_note }}</p>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
