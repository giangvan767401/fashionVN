<x-admin-layout>
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Analytics</h1>
            <p class="text-gray-500 mt-1">Phân tích chuyên sâu về lượt mua và doanh thu của cửa hàng.</p>
        </div>
    </div>

    <!-- Analytics Chart Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Tổng lượt mua</h3>
                <div class="flex items-end gap-3 mt-1">
                    <span id="total-orders" class="text-4xl font-extrabold text-gray-900 tracking-tight">0</span>
                    <div id="growth-badge" class="flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold mb-1 transition-colors">
                        <svg id="growth-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                            <polyline points="16 7 22 7 22 13"></polyline>
                        </svg>
                        <span id="growth-text">0% so với kỳ trước</span>
                    </div>
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="flex flex-col md:flex-row gap-4 items-center">
                <div class="flex items-center gap-2 bg-gray-50 p-1 rounded-xl border border-gray-100">
                    <input type="date" id="custom-start" class="text-sm bg-transparent border-none focus:ring-0 text-gray-500 font-medium px-2 py-1 outline-none">
                    <span class="text-gray-300">-</span>
                    <input type="date" id="custom-end" class="text-sm bg-transparent border-none focus:ring-0 text-gray-500 font-medium px-2 py-1 outline-none">
                    <button type="button" id="btn-custom-date" class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all bg-[#10b981] text-white shadow-sm hover:bg-[#059669]">Lọc</button>
                </div>

                <div class="flex bg-gray-50 rounded-xl p-1 border border-gray-100 filter-group">
                    <button type="button" class="filter-btn px-4 py-2 text-sm font-bold rounded-lg transition-all text-gray-500 hover:text-gray-900" data-period="24h">24h</button>
                    <button type="button" class="filter-btn active px-4 py-2 text-sm font-bold rounded-lg transition-all bg-white text-gray-900 shadow-sm ring-1 ring-gray-900/5" data-period="7d">7 Ngày</button>
                    <button type="button" class="filter-btn px-4 py-2 text-sm font-bold rounded-lg transition-all text-gray-500 hover:text-gray-900" data-period="30d">30 Ngày</button>
                    <button type="button" class="filter-btn px-4 py-2 text-sm font-bold rounded-lg transition-all text-gray-500 hover:text-gray-900" data-period="1y">1 Năm</button>
                </div>
            </div>
        </div>

        <div class="p-6 relative">
            <!-- Skeleton Loading Overlay -->
            <div id="chart-skeleton" class="absolute inset-0 bg-white/80 z-10 flex flex-col items-center justify-center backdrop-blur-sm transition-opacity duration-300">
                <div class="w-full max-w-4xl px-4 animate-pulse">
                    <div class="h-[300px] w-full bg-gray-100 rounded-xl flex items-end px-4 gap-2 pb-4">
                         <!-- Mock bars for skeleton -->
                         @for($i = 0; $i < 12; $i++)
                            <div class="flex-1 bg-gray-200/50 rounded-t-sm" style="height: {{ rand(20, 90) }}%;"></div>
                         @endfor
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 text-[#61715b] font-bold text-sm bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-[#61715b]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Đang tải dữ liệu...
                </div>
            </div>

            <!-- Chart Container -->
            <div id="analytics-chart" class="w-full" style="height: 350px;"></div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let chart = null;
            let currentRevenues = []; // Biến lưu mảng doanh thu để Tooltip đọc
            const primaryColor = '#61715b';
            const skeleton = document.getElementById('chart-skeleton');
            const totalOrdersEl = document.getElementById('total-orders');
            const growthBadge = document.getElementById('growth-badge');
            const growthText = document.getElementById('growth-text');
            const growthIcon = document.getElementById('growth-icon');

            // Định dạng tiền tệ VND
            const formatCurrency = (amount) => {
                return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
            };

            // Options cấu hình mặc định cho ApexCharts
            const getChartOptions = () => {
                return {
                    series: [{
                        name: 'Lượt mua',
                        data: []
                    }],
                    chart: {
                        type: 'area',
                        height: 350,
                        fontFamily: 'Inter, sans-serif',
                        toolbar: {
                            show: true,
                            tools: {
                                zoom: true,
                                zoomin: true,
                                zoomout: true,
                                pan: true,
                                reset: true
                            },
                        },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800
                        }
                    },
                    colors: [primaryColor],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.45,
                            opacityTo: 0.05,
                            stops: [0, 100]
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    xaxis: {
                        categories: [],
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: {
                            style: {
                                colors: '#9ca3af',
                                fontSize: '12px',
                                fontWeight: 500
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#9ca3af',
                                fontSize: '12px',
                                fontWeight: 500
                            },
                            formatter: (value) => { return Math.round(value); }
                        }
                    },
                    grid: {
                        borderColor: '#f3f4f6',
                        strokeDashArray: 4,
                        padding: {
                            top: 0,
                            right: 0,
                            bottom: 0,
                            left: 10
                        }
                    },
                    tooltip: {
                        custom: function({series, seriesIndex, dataPointIndex, w}) {
                            const orders = series[seriesIndex][dataPointIndex];
                            const category = w.globals.categoryLabels[dataPointIndex];
                            const revenue = currentRevenues[dataPointIndex] || 0;

                            return `<div class="bg-gray-900 text-white p-3 rounded-xl shadow-xl text-sm border border-gray-700">
                                <div class="font-bold text-gray-400 mb-2 border-b border-gray-700 pb-1">${category}</div>
                                <div class="flex items-center justify-between gap-4 mb-1">
                                    <span class="text-gray-300">Lượt mua:</span>
                                    <span class="font-bold text-[#10b981]">${orders} đơn</span>
                                </div>
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-gray-300">Doanh thu:</span>
                                    <span class="font-bold text-white">${formatCurrency(revenue)}</span>
                                </div>
                            </div>`;
                        }
                    }
                };
            };

            // Fetch Data
            const fetchAnalytics = async (period, startDate = null, endDate = null) => {
                try {
                    // Show skeleton
                    skeleton.classList.remove('hidden');
                    skeleton.classList.replace('opacity-0', 'opacity-100');

                    let url = `/admin/api/analytics?period=${period}`;
                    if (period === 'custom' && startDate && endDate) {
                        url += `&start_date=${startDate}&end_date=${endDate}`;
                    }

                    const response = await fetch(url);
                    const data = await response.json();

                    currentRevenues = data.revenues;

                    // Update Top Stats
                    totalOrdersEl.innerText = data.totalOrders;
                    
                    if (data.isIncrease) {
                        growthBadge.className = 'flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold mb-1 transition-colors bg-emerald-50 text-emerald-600';
                        growthIcon.innerHTML = `<polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline><polyline points="16 7 22 7 22 13"></polyline>`;
                    } else {
                        growthBadge.className = 'flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold mb-1 transition-colors bg-rose-50 text-rose-600';
                        growthIcon.innerHTML = `<polyline points="22 17 13.5 8.5 8.5 13.5 2 7"></polyline><polyline points="16 17 22 17 22 11"></polyline>`;
                    }
                    growthText.innerText = `${data.percentageChange}% so với kỳ trước`;

                    // Update or Create Chart
                    const options = getChartOptions();
                    options.series[0].data = data.orders;
                    options.xaxis.categories = data.categories;

                    if (chart) {
                        chart.updateOptions({
                            xaxis: { categories: data.categories }
                        });
                        chart.updateSeries([{ data: data.orders }]);
                    } else {
                        chart = new ApexCharts(document.querySelector("#analytics-chart"), options);
                        chart.render();
                    }

                    // Ẩn skeleton khi xong
                    setTimeout(() => {
                        skeleton.classList.replace('opacity-100', 'opacity-0');
                        setTimeout(() => skeleton.classList.add('hidden'), 300);
                    }, 400); // Thêm delay nhỏ để thấy hiệu ứng loading đẹp hơn

                } catch (error) {
                    console.error('Lỗi khi fetch data analytics:', error);
                    skeleton.innerHTML = `<div class="text-rose-500 font-bold bg-rose-50 p-4 rounded-xl">Đã xảy ra lỗi tải dữ liệu.</div>`;
                }
            };

            // Khởi chạy mặc định
            fetchAnalytics('7d');

            // Xử lý click Filter
            const buttons = document.querySelectorAll('.filter-btn');
            
            const wipeFiltersActive = () => {
                buttons.forEach(b => {
                    b.classList.remove('active', 'bg-white', 'text-gray-900', 'shadow-sm', 'ring-1', 'ring-gray-900/5');
                    b.classList.add('text-gray-500', 'hover:text-gray-900');
                });
            };

            buttons.forEach(btn => {
                btn.addEventListener('click', function() {
                    wipeFiltersActive();
                    
                    // Thêm active cho nút hiện tại
                    this.classList.add('active', 'bg-white', 'text-gray-900', 'shadow-sm', 'ring-1', 'ring-gray-900/5');
                    this.classList.remove('text-gray-500', 'hover:text-gray-900');

                    // Reset mảng custom date input (optional)
                    document.getElementById('custom-start').value = '';
                    document.getElementById('custom-end').value = '';

                    // Fetch data
                    fetchAnalytics(this.dataset.period);
                });
            });

            // Xử lý Custom Date Filter
            document.getElementById('btn-custom-date').addEventListener('click', function() {
                const start = document.getElementById('custom-start').value;
                const end = document.getElementById('custom-end').value;

                if (!start || !end) {
                    alert('Vui lòng chọn đầy đủ ngày bắt đầu và kết thúc!');
                    return;
                }
                
                if (new Date(start) > new Date(end)) {
                    alert('Ngày kết thúc phải lớn hơn ngày bắt đầu!');
                    return;
                }

                wipeFiltersActive(); // Tắt active các nút nhanh
                fetchAnalytics('custom', start, end);
            });
        });
    </script>
</x-admin-layout>
