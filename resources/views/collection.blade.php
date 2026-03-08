<x-app-layout>

    <style>
        /* Collection - Heart hover effect */
        .collection-wishlist-btn:hover .wishlist-svg path,
        .collection-wishlist-btn:focus .wishlist-svg path {
            fill: #ef4444;
            stroke: #ef4444;
            transition: fill 0.2s, stroke 0.2s;
        }

        .collection-wishlist-btn .wishlist-svg path {
            transition: fill 0.2s, stroke 0.2s;
        }
    </style>

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
                    @if(request()->has('category'))
                    @foreach((array)request()->get('category') as $catId)
                    <input type="hidden" name="category[]" value="{{ $catId }}">
                    @endforeach
                    @endif
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold font-serif">Filters</h2>
                    </div>

                    <!-- Active Filters Chips -->
                    @php
                    $activeFilters = [];
                    $sortLabels = ['featured' => 'Nổi Bật', 'bestseller' => 'Bán Chạy Nhất', 'price_asc' => 'Giá: Tăng Dần', 'price_desc' => 'Giá: Giảm Dần'];
                    $collectionLabels = ['hang-moi' => 'Hàng Mới', 'ban-chay-nhat' => 'Bán Chạy Nhất'];
                    $materialLabels = ['Cotton' => 'Cotton', 'Linen' => 'Linen', 'Lụa' => 'Lụa'];
                    $colorLabels = ['Đen'=>'Đen','Trắng'=>'Trắng','Be'=>'Be'];
                    $categoryLabels = \App\Models\Category::pluck('name', 'id')->toArray();
                    foreach((array)request()->query('sort', []) as $v) $activeFilters[] = ['param'=>'sort','value'=>$v,'label'=>$sortLabels[$v]??$v];
                    foreach((array)request()->query('collections', []) as $v) $activeFilters[] = ['param'=>'collections','value'=>$v,'label'=>$collectionLabels[$v]??ucfirst($v)];
                    foreach((array)request()->query('sizes', []) as $v) $activeFilters[] = ['param'=>'sizes','value'=>$v,'label'=>$v];
                    foreach((array)request()->query('materials', []) as $v) $activeFilters[] = ['param'=>'materials','value'=>$v,'label'=>$materialLabels[$v]??ucfirst($v)];
                    foreach((array)request()->query('colors', []) as $v) $activeFilters[] = ['param'=>'colors','value'=>$v,'label'=>$colorLabels[$v]??ucfirst($v)];
                    foreach((array)request()->query('category', []) as $v) $activeFilters[] = ['param'=>'category','value'=>$v,'label'=>$categoryLabels[$v]??'Category'];
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
                                    <input type="checkbox" name="collections[]" value="hang-moi" {{ in_array('hang-moi', (array)request()->query('collections', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                    <span class="text-[13px] text-gray-600 ml-4">Hàng Mới</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="collections[]" value="ban-chay-nhat" {{ in_array('ban-chay-nhat', (array)request()->query('collections', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                    <span class="text-[13px] text-gray-600 ml-4">Bán Chạy Nhất</span>
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
                                    <input type="checkbox" name="materials[]" value="Cotton" {{ in_array('Cotton', (array)request()->query('materials', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                    <span class="text-[13px] text-gray-600 ml-4">Cotton</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="materials[]" value="Linen" {{ in_array('Linen', (array)request()->query('materials', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                    <span class="text-[13px] text-gray-600 ml-4">Linen</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="materials[]" value="Lụa" {{ in_array('Lụa', (array)request()->query('materials', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                    <span class="text-[13px] text-gray-600 ml-4">Lụa</span>
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
                                    <input type="checkbox" name="colors[]" value="Đen" {{ in_array('Đen', (array)request()->query('colors', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                    <span class="w-3.5 h-3.5 rounded-full bg-black inline-block border border-gray-300 mx-2 shrink-0"></span>
                                    <span class="text-[13px] text-gray-600 ml-2">Đen</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="colors[]" value="Trắng" {{ in_array('Trắng', (array)request()->query('colors', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                    <span class="w-3.5 h-3.5 rounded-full bg-white inline-block border border-gray-400 mx-2 shrink-0"></span>
                                    <span class="text-[13px] text-gray-600 ml-2">Trắng</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="colors[]" value="Be" {{ in_array('Be', (array)request()->query('colors', [])) ? 'checked' : '' }} class="form-checkbox text-[#5c7a6b] border-gray-300 rounded-sm focus:ring-0 w-3.5 h-3.5 filter-checkbox">
                                    <span class="w-3.5 h-3.5 rounded-full inline-block border border-gray-400 mx-2 shrink-0" style="background-color:#F5F5DC"></span>
                                    <span class="text-[13px] text-gray-600 ml-2">Be</span>
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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                    <div class="product-card group cursor-pointer flex flex-col w-full min-w-0" id="collection-card-{{ $product['first_variant_id'] }}" onclick="window.location.href='{{ route('product.show', ['slug' => $product['slug']]) }}'">

                        {{-- Product Image --}}
                        <div class="block relative w-full aspect-[3/4] bg-[#e6e9e6] overflow-hidden">
                            <img src="{{ $product['image'] }}"
                                onerror="this.src='https://placehold.co/600x800/e2e8f0/a0aec0?text=Sản+Phẩm'"
                                alt="{{ $product['name'] }}"
                                class="absolute inset-0 w-full h-full object-cover mix-blend-multiply transition-transform duration-500 group-hover:scale-105">

                            {{-- NEW badge (top-left) --}}
                            @if($product['is_new'])
                            <span class="absolute top-0 left-0 mt-3 ml-3 z-10 text-[11px] font-semibold tracking-widest text-gray-800 uppercase">NEW</span>
                            @endif

                            {{-- Heart / Wishlist icon (top-right) --}}
                            @auth
                            <button
                                type="button"
                                class="collection-wishlist-btn absolute top-0 right-0 mt-3 mr-3 z-10 w-7 h-7 flex items-center justify-center hover:scale-110 transition-transform"
                                data-variant-id="{{ $product['first_variant_id'] }}"
                                onclick="event.stopPropagation(); toggleCollectionWishlist(this)"
                                title="Thêm vào yêu thích">
                                <svg class="wishlist-svg" width="20" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke="#333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            @else
                            <a href="{{ route('login') }}"
                                class="absolute top-0 right-0 mt-3 mr-3 z-10 w-7 h-7 flex items-center justify-center hover:scale-110 transition-transform"
                                onclick="event.stopPropagation()">
                                <svg width="20" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke="#333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            @endauth
                        </div>

                        {{-- Product Info --}}
                        <div class="mt-3 px-1">
                            {{-- Name + Price on same row --}}
                            <div class="flex justify-between items-start gap-2">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-[14px] leading-snug text-gray-900 group-hover:underline truncate">{{ $product['name'] }}</h3>
                                    @if(!empty($product['description']))
                                    <p class="text-gray-400 text-[12px] mt-0.5 truncate">{{ $product['description'] }}</p>
                                    @endif
                                </div>
                                <span class="font-semibold text-[14px] text-gray-900 whitespace-nowrap ml-2">{{ $product['price'] }}</span>
                            </div>

                            {{-- Color swatches from color_hex --}}
                            @if(!empty($product['colors']))
                            <div class="flex items-center gap-1.5 mt-2">
                                @foreach($product['colors'] as $colorHex)
                                <span
                                    class="inline-block w-3.5 h-3.5 rounded-full border border-gray-200 shadow-sm flex-shrink-0"
                                    style="background-color: {{ $colorHex }};"></span>
                                @endforeach
                            </div>
                            @endif
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

        // Wishlist toggle for product cards
        function toggleCollectionWishlist(btn) {
            const variantId = btn.dataset.variantId;
            if (!variantId) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const svgPath = btn.querySelector('path');

            fetch('/wishlist/toggle', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        variant_id: variantId
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'added') {
                        svgPath.setAttribute('fill', '#ef4444');
                        svgPath.setAttribute('stroke', '#ef4444');
                        btn.title = 'Xóa khỏi yêu thích';
                    } else if (data.status === 'removed') {
                        svgPath.setAttribute('fill', 'none');
                        svgPath.setAttribute('stroke', '#333333');
                        btn.title = 'Thêm vào yêu thích';
                    }
                })
                .catch(err => console.error('Wishlist toggle error:', err));
        }
    </script>
</x-app-layout>