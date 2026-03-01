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
                @if(request()->has('q') && request()->get('q') !== '')
                    <input type="hidden" name="q" value="{{ request('q') }}">
                @endif
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold font-serif">Filters</h2>
                </div>

                <!-- Active Filters Chips -->
                @php
                    $activeFilters = [];
                    $sortLabels = ['featured' => 'Nổi Bật', 'bestseller' => 'Bán Chạy Nhất', 'price_asc' => 'Giá: Tăng Dần', 'price_desc' => 'Giá: Giảm Dần'];
                    $collectionLabels = ['con_hang' => 'Còn Hàng', 'het_hang' => 'Hết Hàng'];
                    $materialLabels = ['cotton' => 'Cotton', 'len' => 'Len', 'len_ban_tong_hop' => 'Len Bản Tổng Hợp', 'lụa' => 'Lụa', 'cashmere' => 'Cashmere'];
                    $colorLabels = ['đen'=>'Đen','đỏ'=>'Đỏ','xanh lá'=>'Xanh Lá','vàng'=>'Vàng','xanh dương'=>'Xanh Dương','tím'=>'Tím','hồng'=>'Hồng','xanh nhạt'=>'Xanh Nhạt','cam'=>'Cam','trắng'=>'Trắng'];
                    foreach((array)request()->query('sort', []) as $v) $activeFilters[] = ['param'=>'sort','value'=>$v,'label'=>$sortLabels[$v]??$v];
                    foreach((array)request()->query('collections', []) as $v) $activeFilters[] = ['param'=>'collections','value'=>$v,'label'=>$collectionLabels[$v]??ucfirst($v)];
                    foreach((array)request()->query('sizes', []) as $v) $activeFilters[] = ['param'=>'sizes','value'=>$v,'label'=>$v];
                    foreach((array)request()->query('materials', []) as $v) $activeFilters[] = ['param'=>'materials','value'=>$v,'label'=>$materialLabels[$v]??ucfirst($v)];
                    foreach((array)request()->query('colors', []) as $v) $activeFilters[] = ['param'=>'colors','value'=>$v,'label'=>$colorLabels[$v]??ucfirst($v)];
                @endphp
                @if(count($activeFilters) > 0)
                <div class="w-full max-w-[200px] mb-5 border border-gray-200 divide-y divide-gray-200">
                    @foreach($activeFilters as $filter)
                    @php
                        $newQuery = request()->query();
                        $newQuery[$filter['param']] = array_values(array_filter((array)request()->query($filter['param'], []), fn($v) => $v !== $filter['value']));
                        if (empty($newQuery[$filter['param']])) unset($newQuery[$filter['param']]);
                        $removeUrl = route('collection') . (count($newQuery) ? '?' . http_build_query($newQuery) : '');
                    @endphp
                    <div class="flex items-center justify-between bg-[#d4d8d3] px-4 py-3">
                        <span class="text-[14px] font-medium text-gray-800 tracking-wide">{{ $filter['label'] }}</span>
                        <a href="{{ $removeUrl }}" class="text-gray-500 hover:text-black ml-8 text-lg leading-none">&times;</a>
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- Collapsible Filters -->
                <div class="space-y-3 max-w-[200px] mt-4">
                    
                    <!-- Sắp Xếp Theo -->
                    <div class="border border-gray-200">
                        <button type="button" class="w-full flex justify-between items-center px-4 py-3 bg-[#7b9c8a] text-white font-semibold text-sm text-left accordion-btn transition-colors" data-target="sort-content">
                            Sắp Xếp Theo
                            <span class="text-lg leading-none icon font-normal">+</span>
                        </button>
                        <div id="sort-content" class="hidden px-4 py-4 space-y-3 bg-white border-t border-gray-200">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="sort[]" value="featured" {{ in_array('featured', (array)request()->query('sort', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="text-[13px] text-gray-600 ml-4">Nổi Bật</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="sort[]" value="bestseller" {{ in_array('bestseller', (array)request()->query('sort', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="text-[13px] text-gray-600 ml-4">Bán Chạy Nhất</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="sort[]" value="price_asc" {{ in_array('price_asc', (array)request()->query('sort', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="text-[13px] text-gray-600 ml-4">Giá: Tăng Dần</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="sort[]" value="price_desc" {{ in_array('price_desc', (array)request()->query('sort', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="text-[13px] text-gray-600 ml-4">Giá: Giảm Dần</span>
                            </label>
                        </div>
                    </div>

                    <!-- Bộ Sưu Tập -->
                    <div class="border border-gray-200">
                        <button type="button" class="w-full flex justify-between items-center px-4 py-3 bg-[#7b9c8a] text-white font-semibold text-sm text-left accordion-btn transition-colors" data-target="collection-content">
                            Bộ Sưu Tập
                            <span class="text-lg leading-none icon font-normal">+</span>
                        </button>
                        <div id="collection-content" class="hidden px-4 py-4 space-y-3 bg-white border-t border-gray-200">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="collections[]" value="con_hang" {{ in_array('con_hang', (array)request()->query('collections', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="text-[13px] text-gray-600 ml-4">Còn Hàng</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="collections[]" value="het_hang" {{ in_array('het_hang', (array)request()->query('collections', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="text-[13px] text-gray-600 ml-4">Hết Hàng</span>
                            </label>
                        </div>
                    </div>

                    <!-- Kích Cỡ -->
                    <div class="border border-gray-200">
                        <button type="button" class="w-full flex justify-between items-center px-4 py-3 bg-[#7b9c8a] text-white font-semibold text-sm text-left accordion-btn transition-colors" data-target="size-content">
                            Kích Cỡ
                            <span class="text-lg leading-none icon font-normal">+</span>
                        </button>
                        <div id="size-content" class="hidden px-4 py-4 space-y-3 bg-white border-t border-gray-200">
                            <label class="flex items-center justify-between cursor-pointer">
                                <input type="checkbox" name="sizes[]" value="XS" {{ in_array('XS', (array)request()->query('sizes', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="text-[13px] text-gray-600 flex-1 ml-4">XS</span>
                            </label>
                            <label class="flex items-center justify-between cursor-pointer">
                                <input type="checkbox" name="sizes[]" value="S" {{ in_array('S', (array)request()->query('sizes', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="text-[13px] text-gray-600 flex-1 ml-4">S</span>
                            </label>
                            <label class="flex items-center justify-between cursor-pointer">
                                <input type="checkbox" name="sizes[]" value="M" {{ in_array('M', (array)request()->query('sizes', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="text-[13px] text-gray-600 flex-1 ml-4">M</span>
                            </label>
                            <label class="flex items-center justify-between cursor-pointer">
                                <input type="checkbox" name="sizes[]" value="L" {{ in_array('L', (array)request()->query('sizes', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="text-[13px] text-gray-600 flex-1 ml-4">L</span>
                            </label>
                            <label class="flex items-center justify-between cursor-pointer">
                                <input type="checkbox" name="sizes[]" value="XL" {{ in_array('XL', (array)request()->query('sizes', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="text-[13px] text-gray-600 flex-1 ml-4">XL</span>
                            </label>
                        </div>
                    </div>

                    <!-- Chất Liệu -->
                    <div class="border border-gray-200">
                        <button type="button" class="w-full flex justify-between items-center px-4 py-3 bg-[#7b9c8a] text-white font-semibold text-sm text-left accordion-btn transition-colors" data-target="materials-content">
                            Chất Liệu
                            <span class="text-lg leading-none icon font-normal">+</span>
                        </button>
                        <div id="materials-content" class="hidden px-4 py-4 space-y-3 bg-white border-t border-gray-200">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="materials[]" value="cotton" {{ in_array('cotton', (array)request()->query('materials', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="text-[13px] text-gray-600 ml-4">Cotton</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="materials[]" value="len" {{ in_array('len', (array)request()->query('materials', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="text-[13px] text-gray-600 ml-4">Len</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="materials[]" value="len_ban_tong_hop" {{ in_array('len_ban_tong_hop', (array)request()->query('materials', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="text-[13px] text-gray-600 ml-4">Len Bản Tổng Hợp</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="materials[]" value="lụa" {{ in_array('lụa', (array)request()->query('materials', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="text-[13px] text-gray-600 ml-4">Lụa</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="materials[]" value="cashmere" {{ in_array('cashmere', (array)request()->query('materials', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="text-[13px] text-gray-600 ml-4">Cashmere</span>
                            </label>
                        </div>
                    </div>

                    <!-- Màu Sắc -->
                    <div class="border border-gray-200">
                        <button type="button" class="w-full flex justify-between items-center px-4 py-3 bg-[#7b9c8a] text-white font-semibold text-sm text-left accordion-btn transition-colors" data-target="colors-content">
                            Màu Sắc
                            <span class="text-lg leading-none icon font-normal">+</span>
                        </button>
                        <div id="colors-content" class="hidden px-4 py-4 space-y-3 bg-white border-t border-gray-200">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="colors[]" value="đen" {{ in_array('đen', (array)request()->query('colors', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="w-3.5 h-3.5 rounded-full bg-black inline-block border border-gray-300 mx-2 shrink-0"></span>
                                <span class="text-[13px] text-gray-600 ml-2">Đen</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="colors[]" value="đỏ" {{ in_array('đỏ', (array)request()->query('colors', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="w-3.5 h-3.5 rounded-full bg-red-600 inline-block border border-gray-300 mx-2 shrink-0"></span>
                                <span class="text-[13px] text-gray-600 ml-2">Đỏ</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="colors[]" value="xanh lá" {{ in_array('xanh lá', (array)request()->query('colors', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="w-3.5 h-3.5 rounded-full bg-green-700 inline-block border border-gray-300 mx-2 shrink-0"></span>
                                <span class="text-[13px] text-gray-600 ml-2">Xanh Lá</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="colors[]" value="vàng" {{ in_array('vàng', (array)request()->query('colors', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="w-3.5 h-3.5 rounded-full bg-yellow-500 inline-block border border-gray-300 mx-2 shrink-0"></span>
                                <span class="text-[13px] text-gray-600 ml-2">Vàng</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="colors[]" value="xanh dương" {{ in_array('xanh dương', (array)request()->query('colors', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="w-3.5 h-3.5 rounded-full bg-blue-600 inline-block border border-gray-300 mx-2 shrink-0"></span>
                                <span class="text-[13px] text-gray-600 ml-2">Xanh Dương</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="colors[]" value="tím" {{ in_array('tím', (array)request()->query('colors', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="w-3.5 h-3.5 rounded-full bg-purple-600 inline-block border border-gray-300 mx-2 shrink-0"></span>
                                <span class="text-[13px] text-gray-600 ml-2">Tím</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="colors[]" value="hồng" {{ in_array('hồng', (array)request()->query('colors', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="w-3.5 h-3.5 rounded-full bg-pink-300 inline-block border border-gray-300 mx-2 shrink-0"></span>
                                <span class="text-[13px] text-gray-600 ml-2">Hồng</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="colors[]" value="xanh nhạt" {{ in_array('xanh nhạt', (array)request()->query('colors', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="w-3.5 h-3.5 rounded-full bg-sky-200 inline-block border border-gray-300 mx-2 shrink-0"></span>
                                <span class="text-[13px] text-gray-600 ml-2">Xanh Nhạt</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="colors[]" value="cam" {{ in_array('cam', (array)request()->query('colors', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="w-3.5 h-3.5 rounded-full bg-orange-500 inline-block border border-gray-300 mx-2 shrink-0"></span>
                                <span class="text-[13px] text-gray-600 ml-2">Cam</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="colors[]" value="trắng" {{ in_array('trắng', (array)request()->query('colors', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                <span class="w-3.5 h-3.5 rounded-full bg-white inline-block border border-gray-400 mx-2 shrink-0"></span>
                                <span class="text-[13px] text-gray-600 ml-2">Trắng</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex flex-row items-center justify-between gap-3">
                    <button type="button" class="w-1/2 text-center text-[12px] text-gray-500 hover:text-black py-2.5 transition-colors font-medium border border-transparent bg-transparent" onclick="window.location.href='{{ route('collection') }}'">Bỏ Chọn Tất Cả</button>
                    <button type="submit" class="w-1/2 bg-[#5c7a6b] text-white px-2 py-2.5 text-[12px] hover:bg-[#4a6356] transition-colors font-medium rounded-sm">Lọc Đã Chọn</button>
                </div>
            </form>
        </div>

        <!-- Product Grid (Phải) -->
        <div class="flex-1">
            @if(request()->has('q') && request()->get('q') !== '')
                <div class="mb-6 pb-4 border-b border-gray-200">
                    <h1 class="text-2xl font-bold">Kết quả tìm kiếm cho: "{{ request('q') }}"</h1>
                    <p class="text-gray-500 mt-2">Tìm thấy {{ count($products) }} sản phẩm</p>
                </div>
            @endif

            @if(count($products) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-12 pl-6">
                @foreach($products as $product)
                <div class="product-card group cursor-pointer relative" onclick="window.location.href='{{ route('product.show', ['slug' => $product['slug']]) }}'">
                    <!-- Image -->
                    <div class="relative w-full aspect-[3/4] overflow-hidden bg-[#e6e9e6] mb-4 p-4">
                        <img src="{{ $product['image'] }}" 
                             onerror="this.src='https://placehold.co/600x800/e2e8f0/a0aec0?text=Sản+Phẩm'"
                             alt="{{ $product['name'] }}" 
                             class="w-full h-full object-contain mix-blend-multiply transition-transform duration-500 group-hover:scale-105">
                        
                        <!-- Actions & Badges -->
                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                            @if($product['is_new'])
                            <span class="bg-white px-3 py-1 text-[11px] font-medium tracking-wide border border-gray-200 uppercase">
                                New
                            </span>
                            @endif
                        </div>

                        <!-- Heart Icon -->
                        <button type="button" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-white/80 flex items-center justify-center hover:bg-white transition-colors z-10" onclick="event.stopPropagation()">
                            <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.5 5.5C16.5 8.5 9 14.5 9 14.5C9 14.5 1.5 8.5 1.5 5.5C1.5 3.5 3 2 5 2C6.5 2 7.7 2.8 8.5 4C9.3 2.8 10.5 2 12 2C14 2 16.5 3.5 16.5 5.5Z" stroke="#333333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Info -->
                    <div class="px-1 mt-3">
                        <div class="flex justify-between items-start mb-1.5 align-top">
                            <div>
                                <h3 class="font-bold text-[15px] leading-tight text-gray-900 group-hover:underline">{{ $product['name'] }}</h3>
                                <p class="text-gray-500 text-[13px] mt-1 line-clamp-1">{{ $product['description'] }}</p>
                            </div>
                            <span class="font-bold text-[15px] text-gray-900 ml-3 whitespace-nowrap">{{ $product['price'] }}</span>
                        </div>
                        
                        <!-- Colors -->
                        <div class="flex items-center gap-1.5 mt-2">
                            @foreach($product['colors'] as $color)
                            <div class="w-3.5 h-3.5 rounded-full border border-gray-300" style="background-color: {{ $color }};"></div>
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

        // Open filters that have checked inputs automatically on page load
        const accordions = [
            'sort-content', 'collection-content', 'size-content', 'materials-content', 'colors-content'
        ];

        accordions.forEach(id => {
            const content = document.getElementById(id);
            if (content) {
                const hasChecked = content.querySelector('input[type="checkbox"]:checked');
                if (hasChecked) {
                    content.classList.remove('hidden');
                    const btn = document.querySelector(`[data-target="${id}"]`);
                    if (btn) btn.querySelector('.icon').textContent = '-';
                }
            }
        });

        // Auto submit form is removed. User must click "Áp Dụng Tùy Chọn".
    });
</script>
@endsection
