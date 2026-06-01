<x-admin-layout>
    <div class="max-w-3xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8 flex items-center space-x-4">
            <a href="{{ route('admin.coupons.index') }}" class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-gray-400 hover:text-emerald-600 transition-all border border-gray-100 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Tạo mã giảm giá mới</h1>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <form action="{{ route('admin.coupons.store') }}" method="POST" class="p-8">
                @csrf
                
                <div class="space-y-6">
                    <!-- Code & Name -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="code" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Mã Giảm Giá (Code)</label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}" required
                                class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 p-3 transition-all uppercase"
                                placeholder="Ví dụ: WELCOME50">
                            @error('code') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="name" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tên Chương Trình / Mô Tả</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 p-3 transition-all"
                                placeholder="Ví dụ: Giảm 50k cho thành viên mới">
                            @error('name') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Type & Value -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="type" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Loại Giảm Giá</label>
                            <select name="type" id="type" required
                                class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 p-3 transition-all">
                                <option value="percent" {{ old('type') == 'percent' ? 'selected' : '' }}>Theo phần trăm (%)</option>
                                <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Số tiền cố định (đ)</option>
                            </select>
                            @error('type') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="value" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Giá Trị Giảm</label>
                            <input type="number" step="0.01" name="value" id="value" value="{{ old('value') }}" required
                                class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 p-3 transition-all"
                                placeholder="Ví dụ: 10 (nếu là %) hoặc 50000 (nếu là đ)">
                            @error('value') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Min Order Amount & Max Discount Amount -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="min_order_amount" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Giá Trị Đơn Hàng Tối Thiểu (đ)</label>
                            <input type="number" step="0.01" name="min_order_amount" id="min_order_amount" value="{{ old('min_order_amount', 0) }}" required
                                class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 p-3 transition-all">
                            @error('min_order_amount') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="max_discount_amount" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Mức Giảm Tối Đa (đ) <span class="text-gray-400 lowercase font-normal">(chỉ áp dụng cho %)</span></label>
                            <input type="number" step="0.01" name="max_discount_amount" id="max_discount_amount" value="{{ old('max_discount_amount') }}"
                                class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 p-3 transition-all"
                                placeholder="Nhập số tiền tối đa được giảm">
                            @error('max_discount_amount') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Usage Limit -->
                    <div>
                        <label for="usage_limit" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Giới Hạn Lượt Sử Dụng (Mã này được sử dụng tối đa bao nhiêu lần)</label>
                        <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit') }}"
                            class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 p-3 transition-all"
                            placeholder="Để trống nếu không giới hạn">
                        @error('usage_limit') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Date range -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="starts_at" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Ngày Bắt Đầu</label>
                            <input type="datetime-local" name="starts_at" id="starts_at" value="{{ old('starts_at') }}"
                                class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 p-3 transition-all">
                            @error('starts_at') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="expires_at" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Ngày Hết Hạn</label>
                            <input type="datetime-local" name="expires_at" id="expires_at" value="{{ old('expires_at') }}"
                                class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 p-3 transition-all">
                            @error('expires_at') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Visibility Toggle -->
                    <div class="flex items-center p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-gray-900">Kích Hoạt Hoạt Động</h4>
                            <p class="text-xs text-gray-500">Mã giảm giá chỉ áp dụng được cho đơn hàng khi ở trạng thái kích hoạt.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', 1) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                </div>

                <div class="mt-10 pt-8 border-t border-gray-50 flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.coupons.index') }}" class="px-6 py-2 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">Hủy</a>
                    <button type="submit" class="px-8 py-2 bg-emerald-600 text-white rounded-xl text-sm font-bold hover:bg-emerald-700 transition-all shadow-md">
                        Lưu Mã Giảm Giá
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
