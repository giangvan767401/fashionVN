<x-app-layout>
<div class="min-h-screen bg-white">
    <!-- Page Header -->
    <div class="text-center pt-12 pb-6">
        <h1 class="text-2xl font-bold tracking-wide text-gray-900">Yêu Thích Của Tôi</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $items->count() }} Sản Phẩm</p>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 pb-20">

        @if($items->isEmpty())
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-gray-200 mb-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Danh sách yêu thích của bạn đang trống</h2>
                <p class="text-sm text-gray-400 mb-8">Hãy khám phá bộ sưu tập và thêm những sản phẩm bạn yêu thích!</p>
                <a href="{{ route('collection') }}" class="inline-block bg-[#61715b] text-white text-sm font-medium tracking-wider uppercase px-10 py-3 hover:bg-[#4a5845] transition-colors">
                    Tiếp Tục Mua Sắm
                </a>
            </div>
        @else
            {{-- Product Grid --}}
            <div class="grid grid-cols-2 lg:grid-cols-4" style="column-gap: 3rem; row-gap: 3.5rem;" id="wishlist-grid">
                @foreach($items as $item)
                    @php
                        $variant  = $item->variant;
                        $product  = $variant->product;
                        $primary  = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                        $imageUrl = $primary ? asset($primary->url) : asset('user/img/default-product.jpg');
                        $price    = $variant->price ?? $product->sale_price ?? $product->base_price;

                        // Gather all color swatches across all variants of this product
                        $colorSwatches = collect();
                        foreach ($product->variants as $v) {
                            $colorAttr = $v->attributeValues->filter(function($a) {
                                return mb_strtolower(optional($a->group)->name, 'UTF-8') === 'màu sắc';
                            })->first();
                            if ($colorAttr && !$colorSwatches->contains('value', $colorAttr->value)) {
                                $colorSwatches->push($colorAttr);
                            }
                        }
                    @endphp

                    <div class="group relative" id="wishlist-item-{{ $variant->id }}" data-variant="{{ $variant->id }}">
                        {{-- Image with overlaid heart button --}}
                        <a href="{{ route('product.show', $product->slug) }}" class="block overflow-hidden bg-gray-50 relative">
                            <div class="aspect-[3/4] overflow-hidden">
                                <img
                                    src="{{ $imageUrl }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                >
                            </div>

                            {{-- Heart Button (top-right of image) --}}
                            <button
                                class="wishlist-heart-btn absolute top-3 right-3 w-8 h-8 flex items-center justify-center bg-white/80 backdrop-blur-sm rounded-full shadow hover:bg-white transition-colors z-10"
                                data-variant="{{ $variant->id }}"
                                title="Xóa khỏi yêu thích"
                                onclick="event.preventDefault(); event.stopPropagation();"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 pointer-events-none" viewBox="0 0 24 24" fill="#ef4444" stroke="#ef4444" stroke-width="1.5">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                            </button>
                        </a>

                        {{-- Info --}}
                        <div class="mt-3 space-y-1">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <a href="{{ route('product.show', $product->slug) }}" class="text-[13px] font-bold text-gray-900 hover:underline leading-snug block">
                                        {{ $product->name }}
                                    </a>
                                    @if($product->subtitle)
                                        <p class="text-[12px] text-gray-500">{{ $product->subtitle }}</p>
                                    @endif
                                </div>
                                <span class="text-[13px] font-semibold text-gray-900 whitespace-nowrap">
                                    {{ number_format($price, 0, ',', '.') }}đ
                                </span>
                            </div>

                            {{-- Color swatches --}}
                            @if($colorSwatches->isNotEmpty())
                                <div class="flex items-center gap-1.5 pt-1">
                                    @foreach($colorSwatches->take(6) as $colorAttr)
                                        @php
                                            $colorName = mb_strtolower($colorAttr->value, 'UTF-8');
                                            $colorMap = [
                                                'đen' => '#1a1a1a',
                                                'trắng' => '#f5f5f5',
                                                'be' => '#d2b48c',
                                                'xanh rêu' => '#61715b',
                                                'sage green' => '#86a17d',
                                                'xanh' => '#4a90d9',
                                                'đỏ' => '#c0392b',
                                                'nâu' => '#8b4513',
                                                'hồng' => '#ffb6c1',
                                                'xám' => '#9e9e9e',
                                                'vàng' => '#f5c842',
                                                'xanh navy' => '#003087',
                                                'xanh lá' => '#3c7a3c',
                                                'olive' => '#6b7c45',
                                                'kem' => '#f5f0e8',
                                                'nude' => '#e8c4a0',
                                                'lavender' => '#c3b1e1',
                                                'mint' => '#98d8c8',
                                                'rust' => '#b7410e',
                                                'camel' => '#c19249',
                                                'tím' => '#7b2d8b',
                                            ];
                                            $swatchColor = $colorMap[$colorName] ?? '#ccc';
                                        @endphp
                                        <span
                                            class="inline-block w-4 h-4 rounded-full border border-gray-300 shadow-sm"
                                            style="background-color: {{ $swatchColor }};"
                                            title="{{ $colorAttr->value }}"
                                        ></span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- AJAX Script for Toggle --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    document.querySelectorAll('.wishlist-heart-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const variantId = this.dataset.variant;
            const card = document.getElementById('wishlist-item-' + variantId);

            fetch('/wishlist/remove/' + variantId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'removed' && card) {
                    card.style.transition = 'opacity 0.3s, transform 0.3s';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        card.remove();
                        // Update count
                        const remaining = document.querySelectorAll('[id^="wishlist-item-"]').length;
                        const countEl = document.querySelector('.wishlist-count');
                        if (countEl) countEl.textContent = remaining + ' Sản Phẩm';

                        // Show empty state if no items left
                        const grid = document.getElementById('wishlist-grid');
                        if (grid && grid.children.length === 0) {
                            grid.innerHTML = '';
                            location.reload();
                        }
                    }, 300);
                }
            })
            .catch(err => console.error('Wishlist error:', err));
        });
    });
});
</script>
</x-app-layout>
