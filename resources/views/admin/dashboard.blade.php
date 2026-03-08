<x-admin-layout>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-500 mt-1">Chào mừng trở lại, {{ Auth::user()->first_name }}. Đây là những gì đang diễn ra với cửa hàng của bạn hôm nay.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Revenue Card -->
        <a href="{{ route('admin.orders.index') }}" class="block bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md hover:border-emerald-200 transition-all cursor-pointer">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Tổng doanh thu</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalRevenue, 0, ',', '.') }}₫</h3>
                </div>
                <div class="p-3 bg-emerald-50 rounded-2xl text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-emerald-500 font-bold text-xs flex items-center gap-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
                    +12.5%
                </span>
                <span class="text-gray-400 text-xs font-medium">so với tháng trước</span>
            </div>
            <!-- Mini Sparkline -->
            <div class="absolute bottom-0 left-0 right-0 h-10 opacity-20 group-hover:opacity-40 transition-opacity">
                <div id="revenue-sparkline"></div>
            </div>
        </a>

        <!-- Users Card -->
        <a href="{{ route('admin.users.index') }}" class="block bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md hover:border-sky-200 transition-all cursor-pointer">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Người dùng hoạt động</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($activeUsers) }}</h3>
                </div>
                <div class="p-3 bg-sky-50 rounded-2xl text-sky-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M17.5 19l2 2 4-4"/></svg>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-emerald-500 font-bold text-xs flex items-center gap-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
                    +8.2%
                </span>
                <span class="text-gray-400 text-xs font-medium">so với tháng trước</span>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-10 opacity-20 group-hover:opacity-40 transition-opacity">
                <div id="users-sparkline"></div>
            </div>
        </a>

        <!-- Orders Card -->
        <a href="{{ route('admin.orders.index') }}" class="block bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md hover:border-indigo-200 transition-all cursor-pointer">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Tổng đơn hàng</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalOrders) }}</h3>
                </div>
                <div class="p-3 bg-indigo-50 rounded-2xl text-indigo-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-rose-500 font-bold text-xs flex items-center gap-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    -3.1%
                </span>
                <span class="text-gray-400 text-xs font-medium">so với tháng trước</span>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-10 opacity-20 group-hover:opacity-40 transition-opacity">
                <div id="orders-sparkline"></div>
            </div>
        </a>

        <!-- Impressions Card -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Lượt xem trang</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($pageViews) }}</h3>
                </div>
                <div class="p-3 bg-amber-50 rounded-2xl text-amber-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0z"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-emerald-500 font-bold text-xs flex items-center gap-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
                    +24.7%
                </span>
                <span class="text-gray-400 text-xs font-medium">so với tháng trước</span>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-10 opacity-20 group-hover:opacity-40 transition-opacity">
                <div id="views-sparkline"></div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Main Area Chart -->
        <div class="lg:col-span-2 bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Tổng quan</h3>
                    <p class="text-gray-400 text-sm">Hiệu suất hàng tháng trong năm nay</p>
                </div>
                <div class="flex bg-gray-50 p-1 rounded-xl">
                    <button class="px-4 py-1.5 bg-white shadow-sm rounded-lg text-sm font-bold text-[#10b981]">Doanh thu</button>
                    <button class="px-4 py-1.5 text-gray-500 rounded-lg text-sm font-medium hover:text-gray-900">Đơn hàng</button>
                    <!-- <button class="px-4 py-1.5 text-gray-500 rounded-lg text-sm font-medium hover:text-gray-900">Profit</button> -->
                </div>
            </div>
            <div id="main-chart" class="h-80"></div>
        </div>

        <!-- Traffic Sources -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-xl font-bold text-gray-900 mb-2">Nguồn truy cập</h3>
            <p class="text-gray-400 text-sm mb-8">Lượng truy cập đến từ đâu</p>
            <div id="traffic-chart" class="h-60 mb-6"></div>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-[#10b981]"></span>
                        <span class="text-sm font-medium text-gray-600">Trực tiếp</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">35%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-[#3b82f6]"></span>
                        <span class="text-sm font-medium text-gray-600">Tự nhiên</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">28%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-[#6366f1]"></span>
                        <span class="text-sm font-medium text-gray-600">Giới thiệu</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">22%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-[#a855f7]"></span>
                        <span class="text-sm font-medium text-gray-600">Mạng xã hội</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">15%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Monthly Goals -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-xl font-bold text-gray-900 mb-2">Mục tiêu tháng</h3>
            <p class="text-gray-400 text-sm mb-8">Theo dõi tiến độ theo mục tiêu</p>
            
            <div class="space-y-8">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-bold text-gray-700">Doanh thu tháng</span>
                        <span class="text-sm font-bold text-emerald-600">{{ $revenueGoalPercent }}%</span>
                    </div>
                    <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $revenueGoalPercent }}%"></div>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">{{ number_format($currentMonthRevenue, 0, ',', '.') }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Mục tiêu: {{ number_format($targetRevenue, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-bold text-gray-700">Khách hàng mới</span>
                        <span class="text-sm font-bold text-sky-600">{{ $usersGoalPercent }}%</span>
                    </div>
                    <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-sky-500 rounded-full" style="width: {{ $usersGoalPercent }}%"></div>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-[10px] font-bold text-gray-400 uppercase">{{ number_format($currentMonthUsers) }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase">Mục tiêu: {{ number_format($targetUsers) }}</span>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-bold text-gray-700">Tỷ lệ chuyển đổi</span>
                        <span class="text-sm font-bold text-indigo-600">{{ $conversionRate }}%</span>
                    </div>
                    <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $conversionRate }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity/Placeholder -->
        <div class="bg-[#10b981] p-8 rounded-2xl shadow-lg shadow-emerald-500/10 relative overflow-hidden group">
            <div class="relative z-10 flex flex-col h-full">
                <h3 class="text-2xl font-bold text-white mb-4">Mùa mới đã sẵn sàng!</h3>
                <p class="text-emerald-50/80 text-lg leading-relaxed mb-8 pr-12">Các chỉ số về lượt xem và đơn hàng đang tăng mạnh. Hãy kiểm tra các danh mục sản phẩm mới nhất để chuẩn bị cho chiến dịch Marketing.</p>
                <div class="mt-auto">
                    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-2 bg-white px-6 py-3 rounded-2xl text-[#10b981] font-bold shadow-xl hover:scale-105 transition-all">
                        Quản lý danh mục
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            <!-- Abstract background shapes -->
            <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-emerald-400/20 rounded-full blur-3xl"></div>
            <div class="absolute right-0 top-0 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Sparklines
            const sparkOptions = {
                chart: { type: 'area', height: 40, sparkline: { enabled: true }, animations: { enabled: true } },
                stroke: { curve: 'smooth', width: 2 },
                fill: { opacity: 0.1 },
                tooltip: { enabled: false }
            };

            const data1 = [20, 40, 30, 50, 40, 60, 55, 75];
            new ApexCharts(document.querySelector("#revenue-sparkline"), { ...sparkOptions, series: [{ data: data1 }], colors: ['#10b981'] }).render();
            new ApexCharts(document.querySelector("#users-sparkline"), { ...sparkOptions, series: [{ data: [30, 25, 45, 40, 55, 50, 65, 60] }], colors: ['#0ea5e9'] }).render();
            new ApexCharts(document.querySelector("#orders-sparkline"), { ...sparkOptions, series: [{ data: [60, 55, 50, 45, 55, 40, 35, 30] }], colors: ['#f43f5e'] }).render();
            new ApexCharts(document.querySelector("#views-sparkline"), { ...sparkOptions, series: [{ data: [10, 25, 35, 40, 65, 80, 85, 95] }], colors: ['#f59e0b'] }).render();

            // Main Overview Chart
            const mainOptions = {
                series: [{
                    name: 'Doanh thu',
                    data: {!! json_encode($revenueData) !!}
                }],
                chart: {
                    type: 'area',
                    height: 320,
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif'
                },
                colors: ['#10b981'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 4 },
                xaxis: {
                    categories: {!! json_encode($chartMonths) !!},
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: { style: { colors: '#9ca3af' }, formatter: (v) => (v / 1000000).toFixed(1) + 'Tr' }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4,
                    xaxis: { lines: { show: true } }
                },
                fill: {
                    type: 'gradient',
                    gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [20, 100] }
                }
            };
            new ApexCharts(document.querySelector("#main-chart"), mainOptions).render();

            // Traffic Sources Chart (Semi-donut style from image)
            const trafficOptions = {
                series: [35, 28, 22, 15],
                chart: { type: 'donut', height: 260 },
                labels: ['Trực tiếp', 'Tự nhiên', 'Giới thiệu', 'Mạng xã hội'],
                colors: ['#10b981', '#3b82f6', '#6366f1', '#a855f7'],
                dataLabels: { enabled: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '80%',
                            labels: {
                                show: true,
                                name: { show: true, fontSize: '12px', fontWeight: 'bold', color: '#9ca3af', offsetY: -10 },
                                value: { show: true, fontSize: '24px', fontWeight: 'bold', color: '#111827', offsetY: 10 },
                                total: { show: true, label: 'Lượt xem', fontSize: '12px', fontWeight: 'bold', color: '#9ca3af', formatter: () => '284K' }
                            }
                        }
                    }
                },
                legend: { show: false }
            };
            new ApexCharts(document.querySelector("#traffic-chart"), trafficOptions).render();
        });
    </script>
</x-admin-layout>
