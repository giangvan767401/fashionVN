@extends('layouts.app')

@section('content')
<div class="font-[Inter] text-[#333333]">
    <!-- Breadcrumb -->
    <div class="max-w-[1440px] px-4 md:px-8 mx-auto mt-8 mb-4">
        <nav class="text-base text-gray-500" aria-label="Breadcrumb">
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
            <form action="{{ route('collection') }}" method="GET" id="filter-form">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Bộ Lọc</h2>
                    <a href="{{ route('collection') }}" class="text-sm text-gray-500 hover:text-black {{ empty(request()->query()) ? 'hidden' : '' }}">Bỏ Chọn Tất Cả</a>
                </div>

                <!-- Accordion Filters -->
                <div class="space-y-3">
                    
                    <!-- Sắp Xếp Theo -->
                    <div class="border border-gray-200">
                        <button type="button" class="w-full flex justify-between items-center px-4 py-3 bg-[#e8edeb] font-medium text-left accordion-btn" data-target="sort-content">
                            Sắp Xếp Theo
                            <span class="text-xl leading-none icon">+</span>
                        </button>
                        <div id="sort-content" class="hidden px-4 py-3 space-y-2 border-t border-gray-200 bg-white">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="sort[]" value="featured" {{ in_array('featured', (array)request()->query('sort', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span>Nổi Bật</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="sort[]" value="bestseller" {{ in_array('bestseller', (array)request()->query('sort', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span>Bán Chạy Nhất</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="sort[]" value="price_asc" {{ in_array('price_asc', (array)request()->query('sort', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span>Giá: Tăng Dần</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="sort[]" value="price_desc" {{ in_array('price_desc', (array)request()->query('sort', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span>Giá: Giảm Dần</span>
                            </label>
                        </div>
                    </div>

                    <!-- Bộ Sưu Tập -->
                    <div class="border border-gray-200">
                        <button type="button" class="w-full flex justify-between items-center px-4 py-3 bg-[#e8edeb] font-medium text-left accordion-btn" data-target="collection-content">
                            Bộ Sưu Tập
                            <span class="text-xl leading-none icon">+</span>
                        </button>
                        <div id="collection-content" class="hidden px-4 py-3 space-y-2 border-t border-gray-200 bg-white">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="collections[]" value="new" {{ in_array('new', (array)request()->query('collections', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span>Hàng Mới</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="collections[]" value="bestseller" {{ in_array('bestseller', (array)request()->query('collections', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span>Bán Chạy Của Tuần</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="collections[]" value="modiweek" {{ in_array('modiweek', (array)request()->query('collections', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span>Modiweek</span>
                            </label>
                        </div>
                    </div>

                    <!-- Kích Cỡ -->
                    <div class="border border-gray-200">
                        <button type="button" class="w-full flex justify-between items-center px-4 py-3 bg-[#e8edeb] font-medium text-left accordion-btn" data-target="size-content">
                            Kích Cỡ
                            <span class="text-xl leading-none icon">+</span>
                        </button>
                        <div id="size-content" class="hidden px-4 py-3 space-y-2 border-t border-gray-200 bg-white">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="sizes[]" value="XS" {{ in_array('XS', (array)request()->query('sizes', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span>XS</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="sizes[]" value="S" {{ in_array('S', (array)request()->query('sizes', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span>S</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="sizes[]" value="M" {{ in_array('M', (array)request()->query('sizes', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span>M</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="sizes[]" value="L" {{ in_array('L', (array)request()->query('sizes', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span>L</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="sizes[]" value="XL" {{ in_array('XL', (array)request()->query('sizes', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span>XL</span>
                            </label>
                        </div>
                    </div>

                    <!-- Chất Liệu -->
                    <div class="border border-gray-200 bg-[#7b9c8a] text-white">
                        <button type="button" class="w-full flex justify-between items-center px-4 py-3 font-medium text-left accordion-btn" data-target="material-content">
                            Chất Liệu
                            <span class="text-xl leading-none icon">+</span>
                        </button>
                        <div id="material-content" class="hidden px-4 py-3 space-y-2 bg-white text-black border-t border-gray-200">
                             <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="materials[]" value="cotton" {{ in_array('cotton', (array)request()->query('materials', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span>Cotton</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="materials[]" value="wool" {{ in_array('wool', (array)request()->query('materials', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span>Len</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="materials[]" value="wool-blend" {{ in_array('wool-blend', (array)request()->query('materials', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span>Len Bán Tổng Hợp</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="materials[]" value="silk" {{ in_array('silk', (array)request()->query('materials', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span>Lụa</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="materials[]" value="linen" {{ in_array('linen', (array)request()->query('materials', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span>Vải Linen</span>
                            </label>
                        </div>
                    </div>

                    <!-- Màu Sắc -->
                    <div class="border border-gray-200">
                        <button type="button" class="w-full flex justify-between items-center px-4 py-3 bg-[#7b9c8a] text-white font-medium text-left accordion-btn" data-target="color-content">
                            Màu Sắc
                            <span class="text-xl leading-none icon">+</span>
                        </button>
                        <div id="color-content" class="hidden px-4 py-3 grid grid-cols-2 gap-y-2 border-t border-gray-200 bg-white">
                            <label class="flex items-center space-x-2 cursor-pointer text-black">
                                <input type="checkbox" name="colors[]" value="black" {{ in_array('black', (array)request()->query('colors', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span class="w-4 h-4 rounded-full bg-black inline-block border border-gray-200"></span>
                                <span class="text-sm">Đen</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer text-black">
                                <input type="checkbox" name="colors[]" value="red" {{ in_array('red', (array)request()->query('colors', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span class="w-4 h-4 rounded-full bg-red-600 inline-block border border-gray-200"></span>
                                <span class="text-sm">Đỏ</span>
                            </label>
                             <label class="flex items-center space-x-2 cursor-pointer text-black">
                                <input type="checkbox" name="colors[]" value="green" {{ in_array('green', (array)request()->query('colors', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span class="w-4 h-4 rounded-full bg-[#9ACD32] inline-block border border-gray-200"></span>
                                <span class="text-sm">Xanh Lá</span>
                            </label>
                             <label class="flex items-center space-x-2 cursor-pointer text-black">
                                <input type="checkbox" name="colors[]" value="yellow" {{ in_array('yellow', (array)request()->query('colors', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span class="w-4 h-4 rounded-full bg-yellow-500 inline-block border border-gray-200"></span>
                                <span class="text-sm">Vàng</span>
                            </label>
                             <label class="flex items-center space-x-2 cursor-pointer text-black">
                                <input type="checkbox" name="colors[]" value="blue" {{ in_array('blue', (array)request()->query('colors', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] rounded-sm focus:ring-0 w-4 h-4 filter-checkbox">
                                <span class="w-4 h-4 rounded-full bg-blue-700 inline-block border border-gray-200"></span>
                                <span class="text-sm">Xanh Dương</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Desktop Apply Button -->
                <div class="mt-6 flex flex-col gap-3">
                     <button type="submit" class="w-full bg-[#5c7a6b] text-white px-6 py-3 hover:bg-[#4a6356] transition-colors font-medium">Áp Dụng Tùy Chọn</button>
                     <a href="{{ route('collection') }}" class="w-full text-center py-2 text-sm text-gray-500 hover:text-black hover:underline {{ empty(request()->query()) ? 'hidden' : '' }}">Thoát / Xóa bộ lọc</a>
                </div>
            </form>
        </div>

        <!-- Product Grid (Phải) -->
        <div class="flex-1">
            @if(count($products) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-12">
                @foreach($products as $product)
                <div class="product-card group cursor-pointer relative" onclick="window.location.href='{{ route('product.show', ['slug' => 'san-pham-mau']) }}'">
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
                        <button type="button" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-white/80 flex items-center justify-center hover:bg-white transition-colors z-10" onclick="event.stopPropagation()">
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
            @else
            <div class="py-20 text-center text-gray-500 border border-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto mb-4 text-gray-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75l-2.489-2.489m0 0a3.375 3.375 0 10-4.773-4.773 3.375 3.375 0 004.774 4.774zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-lg">Không tìm thấy sản phẩm nào phù hợp với bộ lọc.</p>
                <a href="{{ route('collection') }}" class="text-[#5c7a6b] font-medium hover:underline mt-2 inline-block">Bỏ chọn các bộ lọc</a>
            </div>
            @endif
            
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

        // Set active state for sort and material accordions for UI purpose
        document.querySelector('[data-target="sort-content"]').click();
        
        const matBtn = document.querySelector('[data-target="material-content"]');
        if (matBtn) {
            matBtn.click();
            matBtn.classList.remove('bg-[#7b9c8a]', 'text-white');
            matBtn.classList.add('text-black');
        }
    });
</script>
@endsection
