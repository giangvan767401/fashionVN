<x-admin-layout>
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quản lý Voucher / Khuyến mãi</h1>
                <p class="text-sm text-gray-500 mt-1">Quản lý các mã giảm giá áp dụng cho khách hàng khi mua hàng.</p>
            </div>
            <a href="{{ route('admin.coupons.create') }}" class="px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-bold hover:bg-emerald-700 transition-all shadow-md flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Thêm mã giảm giá
            </a>
        </div>
        
        @if (session('status'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 shadow-sm rounded-r-lg flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 text-gray-400 text-[11px] uppercase tracking-wider font-bold border-b border-gray-100">
                            <th class="px-6 py-4">Mã & Tên Voucher</th>
                            <th class="px-6 py-4">Mức Giảm</th>
                            <th class="px-6 py-4">Điều Kiện</th>
                            <th class="px-6 py-4 text-center">Giới Hạn / Đã Dùng</th>
                            <th class="px-6 py-4 text-center">Thời Gian Hạn</th>
                            <th class="px-6 py-4 text-center">Trạng Thái</th>
                            <th class="px-6 py-4 text-right">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($coupons as $coupon)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600 font-bold mr-3 border border-emerald-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 0 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 0 1 0-4V7a2 2 0 0 0-2-2H5z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-mono font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded-md inline-block uppercase tracking-wider">{{ $coupon->code }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">{{ $coupon->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                @if($coupon->type === 'percent')
                                    {{ number_format($coupon->value, 0) }}% 
                                    @if($coupon->max_discount_amount)
                                        <div class="text-[10px] text-gray-400 font-normal">Tối đa {{ number_format($coupon->max_discount_amount, 0, ',', '.') }}đ</div>
                                    @endif
                                @else
                                    {{ number_format($coupon->value, 0, ',', '.') }}đ
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                Đơn từ: <span class="font-semibold text-gray-900">{{ number_format($coupon->min_order_amount, 0, ',', '.') }}đ</span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-medium text-gray-600">
                                <div class="text-xs">
                                    <span class="text-emerald-600 font-bold">{{ $coupon->used_count }}</span>
                                    <span class="text-gray-400">/</span>
                                    <span class="text-gray-900">{{ $coupon->usage_limit ?? '∞' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-xs text-gray-500 font-medium">
                                @if($coupon->starts_at || $coupon->expires_at)
                                    <div>Từ: {{ $coupon->starts_at ? $coupon->starts_at->format('d/m/Y H:i') : 'N/A' }}</div>
                                    <div class="mt-0.5">Đến: {{ $coupon->expires_at ? $coupon->expires_at->format('d/m/Y H:i') : 'N/A' }}</div>
                                @else
                                    <span class="text-gray-400">Không thời hạn</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('admin.coupons.toggle-status', $coupon) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center px-2.5 py-1 {{ $coupon->is_active ? 'bg-emerald-50 text-emerald-600 border-emerald-100 hover:bg-emerald-100' : 'bg-gray-50 text-gray-400 border-gray-100 hover:bg-gray-100' }} text-[10px] font-bold rounded-full border uppercase transition-colors" title="Nhấn để {{ $coupon->is_active ? 'Ẩn' : 'Hiện' }}">
                                        {{ $coupon->is_active ? 'Kích hoạt' : 'Tạm ẩn' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors border border-transparent hover:border-emerald-100" title="Chỉnh sửa">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>

                                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa mã giảm giá này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-100 rounded-lg transition-colors border border-transparent shadow-sm" title="Xóa">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500 text-sm">
                                Chưa có mã giảm giá nào được tạo. Hãy tạo mã giảm giá đầu tiên!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($coupons->hasPages())
            <div class="p-6 bg-gray-50/50 border-t border-gray-100">
                {{ $coupons->links() }}
            </div>
            @endif
        </div>
    </div>
</x-admin-layout>
