<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b border-gray-200">
                    <h3 class="text-lg font-medium">Chào mừng, {{ Auth::user()->full_name }}!</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Đây là trang quản trị demo dành riêng cho Role Admin.
                    </p>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Thống kê giả định -->
                        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                            <span class="text-indigo-600 font-bold text-2xl">120</span>
                            <p class="text-xs text-indigo-800 uppercase tracking-wider font-semibold">Sản phẩm</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                            <span class="text-green-600 font-bold text-2xl">45</span>
                            <p class="text-xs text-green-800 uppercase tracking-wider font-semibold">Đơn hàng mới</p>
                        </div>
                        <div class="bg-amber-50 p-4 rounded-lg border border-amber-100">
                            <span class="text-amber-600 font-bold text-2xl">12k</span>
                            <p class="text-xs text-amber-800 uppercase tracking-wider font-semibold">Khách hàng</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-gray-50 border-t border-gray-100">
                    <p class="text-xs text-gray-500 italic">
                        Bạn có toàn quyền quản lý hệ thống tại đây. Các chức năng tiếp theo sẽ được xây dựng sau.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
