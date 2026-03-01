@extends('layouts.app')

@section('content')
<div class="font-[Inter] text-[#333333]">
    <!-- Breadcrumb -->
    <div class="max-w-[1440px] px-4 md:px-8 mx-auto mt-8 mb-4">
        <nav class="text-sm text-gray-500" aria-label="Breadcrumb">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="/" class="hover:text-gray-900 transition-colors">Trang Chủ</a>
                    <span class="mx-2">/</span>
                </li>
                <li class="flex items-center">
                    <span class="text-gray-900">Tất Cả Sản Phẩm</span>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Banner -->
   <div class="relative w-full h-[600px] md:h-[calc(100vh-64px)] overflow-hidden bg-gray-100 flex items-center justify-center">
    <img src="{{ asset('user/img/collection.png') }}" 
         class="max-w-full max-h-full w-auto h-auto object-contain" alt="Collection Banner">
</div>

    <!-- Main Layout -->
    <div class="max-w-[1440px] px-4 md:px-8 mx-auto flex flex-col md:flex-row gap-8 mb-20">
        
        <!-- Sidebar Filter (Trái) -->
        <div class="w-full md:w-[280px] flex-shrink-0">
            <h2 class="text-2xl font-bold mb-6">Bộ Lọc</h2>

            <!-- Lọc Đã Chọn (Chips) - Ẩn mặc định, hiện khi có chọn -->
            <div class="mb-6 hidden" id="active-filters">
                <div class="flex flex-wrap gap-2 mb-4" id="filter-chips">
                    <!-- Chips will be appended here by JS -->
                </div>
                <div class="flex justify-between items-center text-sm">
                    <button class="text-gray-500 hover:text-black underline underline-offset-2">Bỏ Chọn Tất Cả</button>
                    <button class="bg-[#5c7a6b] text-white px-4 py-2 hover:bg-[#4a6356] transition-colors rounded-sm">Lọc Đã Chọn</button>
                </div>
            </div>

            <!-- Accordion Filters -->
            <div class="space-y-3">
                
                <!-- Sắp Xếp Theo -->
                <div class="border border-gray-200">
                    <button class="w-full flex justify-between items-center px-4 py-3 bg-[#e8edeb] font-medium text-left accordion-btn" data-target="sort-content">
                        Sắp Xếp Theo
                        <span class="text-xl leading-none icon">+</span>
                    </button>
                    <div id="sort-content" class="hidden px-4 py-3 space-y-2 border-t border-gray-200 bg-white">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span>Nổi Bật</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span>Bán Chạy Nhất</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span>Giá: Tăng Dần</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span>Giá: Giảm Dần</span>
                        </label>
                    </div>
                </div>

                <!-- Bộ Sưu Tập -->
                <div class="border border-gray-200">
                    <button class="w-full flex justify-between items-center px-4 py-3 bg-[#e8edeb] font-medium text-left accordion-btn" data-target="collection-content">
                        Bộ Sưu Tập
                        <span class="text-xl leading-none icon">+</span>
                    </button>
                    <div id="collection-content" class="hidden px-4 py-3 space-y-2 border-t border-gray-200 bg-white">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span>Hàng Mới</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span>Bán Chạy Của Tuần</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span>Modiweek</span>
                        </label>
                    </div>
                </div>

                <!-- Kích Cỡ -->
                <div class="border border-gray-200">
                    <button class="w-full flex justify-between items-center px-4 py-3 bg-[#e8edeb] font-medium text-left accordion-btn" data-target="size-content">
                        Kích Cỡ
                        <span class="text-xl leading-none icon">+</span>
                    </button>
                    <div id="size-content" class="hidden px-4 py-3 space-y-2 border-t border-gray-200 bg-white">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span>XS</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span>S</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span>M</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span>L</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span>XL</span>
                        </label>
                    </div>
                </div>

                <!-- Chất Liệu -->
                <div class="border border-gray-200 bg-[#7b9c8a] text-white">
                    <button class="w-full flex justify-between items-center px-4 py-3 font-medium text-left accordion-btn" data-target="material-content">
                        Chất Liệu
                        <span class="text-xl leading-none icon">+</span>
                    </button>
                    <div id="material-content" class="hidden px-4 py-3 space-y-2 bg-white text-black border-t border-gray-200">
                         <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span>Cotton</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span>Len</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span>Len Bán Tổng Hợp</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span>Lụa</span>
                        </label>
                    </div>
                </div>

                <!-- Màu Sắc -->
                <div class="border border-gray-200">
                    <button class="w-full flex justify-between items-center px-4 py-3 bg-[#7b9c8a] text-white font-medium text-left accordion-btn" data-target="color-content">
                        Màu Sắc
                        <span class="text-xl leading-none icon">+</span>
                    </button>
                    <div id="color-content" class="hidden px-4 py-3 grid grid-cols-2 gap-y-2 border-t border-gray-200 bg-white">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span class="w-4 h-4 rounded-full bg-black inline-block border border-gray-200"></span>
                            <span class="text-sm">Đen</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span class="w-4 h-4 rounded-full bg-red-600 inline-block border border-gray-200"></span>
                            <span class="text-sm">Đỏ</span>
                        </label>
                         <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span class="w-4 h-4 rounded-full bg-green-700 inline-block border border-gray-200"></span>
                            <span class="text-sm">Xanh Lá</span>
                        </label>
                         <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span class="w-4 h-4 rounded-full bg-yellow-500 inline-block border border-gray-200"></span>
                            <span class="text-sm">Vàng</span>
                        </label>
                         <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span class="w-4 h-4 rounded-full bg-blue-700 inline-block border border-gray-200"></span>
                            <span class="text-sm">Xanh Dương</span>
                        </label>
                         <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4">
                            <span class="w-4 h-4 rounded-full bg-purple-500 inline-block border border-gray-200"></span>
                            <span class="text-sm">Tím</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-between items-center">
                 <button class="text-sm text-gray-500 hover:text-black">Bỏ Chọn Tất Cả</button>
                 <button class="bg-[#5c7a6b] text-white px-6 py-2 hover:bg-[#4a6356] transition-colors font-medium">Lọc Đã Chọn</button>
            </div>
        </div>

        <!-- Product Grid (Phải) -->
        <div class="flex-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-12">
                @foreach($products as $product)
                <div class="product-card group cursor-pointer relative">
                    <!-- Image -->
                    <div class="relative w-full aspect-[3/4] overflow-hidden bg-gray-100 mb-4">
                        <img src="{{ $product['image'] }}" 
                             onerror="this.src='https://placehold.co/600x800/e2e8f0/a0aec0?text=Sản+Phẩm'"
                             alt="{{ $product['name'] }}" 
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        
                        <!-- Badge -->
                        @if($product['is_new'])
                        <div class="absolute top-4 left-4 bg-white px-3 py-1 text-xs font-medium tracking-wide shadow-sm">
                            New
                        </div>
                        @endif

                        <!-- Heart Icon -->
                        <button class="absolute top-4 right-4 w-8 h-8 rounded-full bg-white/80 flex items-center justify-center hover:bg-white transition-colors z-10">
                            <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.5 5.5C16.5 8.5 9 14.5 9 14.5C9 14.5 1.5 8.5 1.5 5.5C1.5 3.5 3 2 5 2C6.5 2 7.7 2.8 8.5 4C9.3 2.8 10.5 2 12 2C14 2 16.5 3.5 16.5 5.5Z" stroke="#333333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Info -->
                    <div>
                        <h3 class="font-bold text-lg leading-tight mb-1">{{ $product['name'] }}</h3>
                        <div class="flex justify-between items-start mb-2">
                            <p class="text-gray-600 text-sm">{{ $product['description'] }}</p>
                            <span class="font-bold text-lg">{{ $product['price'] }}</span>
                        </div>
                        
                        <!-- Colors -->
                        <div class="flex items-center gap-2 mt-3">
                            @foreach($product['colors'] as $color)
                            <div class="w-4 h-4 rounded-full border border-gray-300" style="background-color: {{ $color }};"></div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Load More -->
            <div class="mt-16 flex justify-center border-t border-gray-200 pt-16">
                <button class="border border-gray-300 px-8 py-3 text-sm font-medium hover:bg-gray-50 transition-colors">
                    Load More
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const accordionBtns = document.querySelectorAll('.accordion-btn');
        
        accordionBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const content = document.getElementById(targetId);
                const icon = this.querySelector('.icon');
                
                // Toggle this tab
                if (content.classList.contains('hidden')) {
                    content.classList.remove('hidden');
                    icon.textContent = '-';
                    // Optional: remove background color classes to match active state if needed
                    // this.classList.remove('bg-[#e8edeb]');
                } else {
                    content.classList.add('hidden');
                    icon.textContent = '+';
                }
            });
        });

        // Set an active state to Sắp Xếp Theo & Chất Liệu as shown in design
        document.querySelector('[data-target="sort-content"]').click();
        
        const matBtn = document.querySelector('[data-target="material-content"]');
        matBtn.click();
        matBtn.classList.remove('bg-[#7b9c8a]', 'text-white');
        matBtn.classList.add('text-black');
    });
</script>
@endsection
