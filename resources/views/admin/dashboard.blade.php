<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard Quản trị</h1>
                    <p class="text-sm text-gray-500 mt-1">Chào mừng trở lại, <span class="font-semibold text-indigo-600">{{ Auth::user()->full_name }}</span>. Đây là giao diện quản lý trải nghiệm sản phẩm.</p>
                </div>
                <div class="flex space-x-3">
                    <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors shadow-sm">Xuất báo cáo</button>
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-all shadow-sm shadow-indigo-200">+ Thêm sản phẩm</button>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Stat Card 1 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.91 8.18 12 17l-8.91-8.82L4.41 5.6l7.59 7.52 7.59-7.52z"/></svg>
                        </div>
                        <span class="text-xs font-bold text-green-500 bg-green-50 px-2 py-1 rounded-full">+12%</span>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Tổng doanh thu</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">128.500.000đ</h3>
                </div>

                <!-- Stat Card 2 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                        </div>
                        <span class="text-xs font-bold text-blue-500 bg-blue-50 px-2 py-1 rounded-full">480 mới</span>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Đơn hàng</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">2,450</h3>
                </div>

                <!-- Stat Card 3 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <span class="text-xs font-bold text-amber-500 bg-amber-50 px-2 py-1 rounded-full">24 Online</span>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Khách hàng</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">15,700</h3>
                </div>

                <!-- Stat Card 4 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-rose-50 rounded-xl flex items-center justify-center text-rose-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <span class="text-xs font-bold text-rose-500 bg-rose-50 px-2 py-1 rounded-full">99+ SKU</span>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Sản phẩm</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">1,200</h3>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Recent Orders Table -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                        <h3 class="font-bold text-gray-900">Đơn hàng gần đây</h3>
                        <a href="#" class="text-sm text-indigo-600 font-medium hover:text-indigo-700">Xem tất cả</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50/50 text-gray-400 text-[10px] uppercase tracking-wider font-bold">
                                    <th class="px-6 py-3">Mã đơn</th>
                                    <th class="px-6 py-3">Khách hàng</th>
                                    <th class="px-6 py-3">Trạng thái</th>
                                    <th class="px-6 py-3 text-right">Tổng tiền</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">#ORD-7721</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">Nguyễn Văn A</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-green-50 text-green-600 text-[10px] font-bold rounded-full">ĐÃ GIAO</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">1.250.000đ</td>
                                </tr>
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">#ORD-7720</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">Trần Thị B</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-amber-50 text-amber-600 text-[10px] font-bold rounded-full">ĐANG XỬ LÝ</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">890.000đ</td>
                                </tr>
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">#ORD-7719</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">Lê Văn C</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-blue-50 text-blue-600 text-[10px] font-bold rounded-full">ĐANG GIAO</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">2.100.000đ</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Actions/Info -->
                <div class="space-y-6">
                    <div class="bg-indigo-600 rounded-2xl p-6 text-white shadow-lg shadow-indigo-100 relative overflow-hidden">
                        <div class="relative z-10">
                            <h3 class="font-bold text-lg mb-2">Thông báo hệ thống</h3>
                            <p class="text-indigo-100 text-sm mb-4">Hệ thống sẽ được bảo trì vào lúc 02:00 sáng mai. Vui lòng hoàn tất các tác vụ quan trọng.</p>
                            <button class="w-full py-2 bg-white/20 hover:bg-white/30 transition-colors text-white rounded-lg text-sm font-medium backdrop-blur-sm">Chi tiết</button>
                        </div>
                        <!-- Abstract circles for style -->
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                        <div class="absolute -left-4 -bottom-4 w-24 h-24 bg-indigo-400/20 rounded-full blur-2xl"></div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-900 mb-4">Ghi chú nhanh</h3>
                        <textarea class="w-full h-32 bg-gray-50 border-none rounded-xl text-sm text-gray-600 focus:ring-1 focus:ring-indigo-100 placeholder:text-gray-400 p-4" placeholder="Nhập ghi chú tại đây..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
