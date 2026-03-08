<x-app-layout>

<style>
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

    {{-- Banner --}}
    <div class="relative w-full h-[400px] md:h-[500px] overflow-hidden bg-gray-100 flex items-center justify-center">
        <img src="{{ asset('user/img/collection.png') }}"
             class="max-w-full max-h-full w-auto h-auto object-contain" alt="Collection Banner">
    </div>

    <div class="max-w-[1440px] px-4 md:px-8 mx-auto py-10 mb-20">

        {{-- Tiêu đề --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Tất Cả Sản Phẩm</h1>
            <p class="text-gray-500 mt-1 text-sm">{{ count($products) }} sản phẩm</p>
        </div>

        {{-- Thanh tìm kiếm + bộ lọc (1 hàng) --}}
        <form action="{{ route('collection') }}" method="GET"
              class="flex flex-col md:flex-row gap-3 mb-8 items-stretch md:items-center">

            {{-- Ô tìm kiếm --}}
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                </span>
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Tìm kiếm sản phẩm..."
                    class="w-full border border-gray-300 pl-9 pr-4 py-2.5 text-sm rounded-sm focus:outline-none focus:border-[#5c7a6b] focus:ring-1 focus:ring-[#5c7a6b] bg-white"
                >
            </div>

            {{-- Lọc theo danh mục --}}
            <select name="category"
                    class="border border-gray-300 px-3 py-2.5 text-sm rounded-sm bg-white focus:outline-none focus:border-[#5c7a6b] md:w-52">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            {{-- Sắp xếp theo giá --}}
            <select name="sort"
                    class="border border-gray-300 px-3 py-2.5 text-sm rounded-sm bg-white focus:outline-none focus:border-[#5c7a6b] md:w-48">
                <option value="">Mới nhất</option>
                <option value="price_asc"  {{ request('sort') === 'price_asc'  ? 'selected' : '' }}>Giá: Tăng Dần</option>
                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Giá: Giảm Dần</option>
            </select>

            {{-- Nút tìm kiếm --}}
            <button type="submit"
                    class="bg-[#5c7a6b] text-white px-6 py-2.5 text-sm font-medium hover:bg-[#4a6356] transition-colors rounded-sm whitespace-nowrap">
                Tìm / Lọc
            </button>

            {{-- Nút bỏ lọc (chỉ hiện khi có filter) --}}
            @if(request('q') || request('category') || request('sort'))
            <a href="{{ route('collection') }}"
               class="border border-gray-300 text-gray-600 px-5 py-2.5 text-sm font-medium hover:bg-gray-50 transition-colors rounded-sm whitespace-nowrap text-center">
                Xóa Lọc
            </a>
            @endif
        </form>

        {{-- Kết quả tìm kiếm --}}
        @if(request('q'))
        <div class="mb-6 pb-3 border-b border-gray-200">
            <p class="text-gray-600 text-sm">
                Kết quả tìm kiếm cho: <span class="font-semibold text-gray-900">"{{ request('q') }}"</span>
                – tìm thấy <span class="font-semibold">{{ count($products) }}</span> sản phẩm
            </p>
        </div>
        @endif

        {{-- Danh sách sản phẩm --}}
        @if(count($products) > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-10">
            @foreach($products as $product)
            <div class="group cursor-pointer flex flex-col"
                 onclick="window.location.href='{{ route('product.show', ['slug' => $product['slug']]) }}'">

                {{-- Ảnh sản phẩm --}}
                <div class="relative w-full aspect-[3/4] bg-[#e6e9e6] overflow-hidden">
                    <img src="{{ $product['image'] }}"
                         onerror="this.src='https://placehold.co/600x800/e2e8f0/a0aec0?text=Sản+Phẩm'"
                         alt="{{ $product['name'] }}"
                         class="absolute inset-0 w-full h-full object-cover mix-blend-multiply transition-transform duration-500 group-hover:scale-105">

                    {{-- Nút yêu thích --}}
                    @auth
                    <button
                        type="button"
                        class="collection-wishlist-btn absolute top-0 right-0 mt-3 mr-3 z-10 w-7 h-7 flex items-center justify-center hover:scale-110 transition-transform"
                        data-variant-id="{{ $product['first_variant_id'] }}"
                        onclick="event.stopPropagation(); toggleCollectionWishlist(this)"
                        title="Thêm vào yêu thích">
                        <svg class="wishlist-svg" width="20" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke="#333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    @endauth
                </div>

                {{-- Thông tin sản phẩm --}}
                <div class="mt-3 px-1">
                    <div class="flex justify-between items-start gap-2">
                        <h3 class="font-semibold text-[14px] leading-snug text-gray-900 group-hover:underline flex-1 min-w-0 truncate">
                            {{ $product['name'] }}
                        </h3>
                        <span class="font-semibold text-[14px] text-gray-900 whitespace-nowrap ml-2">
                            {{ $product['price'] }}
                        </span>
                    </div>
                    @if(!empty($product['description']))
                    <p class="text-gray-400 text-[12px] mt-0.5 truncate">{{ $product['description'] }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        @else
        {{-- Không có kết quả --}}
        <div class="py-24 text-center border border-dashed border-gray-200 rounded-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto mb-4 text-gray-300">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>
            <p class="text-lg text-gray-500">Không tìm thấy sản phẩm nào phù hợp.</p>
            <a href="{{ route('collection') }}" class="inline-block mt-4 text-[#5c7a6b] font-medium hover:underline text-sm">
                Xem tất cả sản phẩm
            </a>
        </div>
        @endif

    </div>
</div>

<script>
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
        body: JSON.stringify({ variant_id: variantId })
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
    .catch(err => console.error('Wishlist error:', err));
}
</script>

</x-app-layout>
