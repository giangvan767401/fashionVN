@props(['product'])

@php
    $primaryImage = $product->images->where('is_primary', true)->first();
    $isNew = $product->created_at->diffInDays(now()) < 7 || $product->is_featured;
@endphp

<a href="{{ route('product.show', $product->slug) }}" class="group cursor-pointer block">
    <div class="relative aspect-[3/4] overflow-hidden mb-5 bg-[#f5f5f5]">
        <!-- Image with hover zoom -->
        @if($primaryImage)
            @php
                $imgUrl = Str::startsWith($primaryImage->url, 'http') 
                    ? $primaryImage->url 
                    : (Str::startsWith($primaryImage->url, 'images/') ? asset($primaryImage->url) : asset('storage/' . $primaryImage->url));
            @endphp
            <img src="{{ $imgUrl }}" 
                 alt="{{ $product->name }}" 
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
            </div>
        @endif

        <!-- Labels -->
        @if($isNew)
            <div class="absolute top-4 left-4">
                <span class="text-[11px] font-bold text-black uppercase tracking-[0.2em]">NEW</span>
            </div>
        @endif

        <!-- Wishlist Icon -->
        <div class="absolute top-4 right-4">
            <button class="text-black/30 hover:text-black transition-colors" onclick="event.preventDefault()">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
            </button>
        </div>
    </div>

    <!-- Product Details -->
    <div class="space-y-1.5">
        <div class="flex items-center justify-between gap-4">
            <h3 class="text-[13px] font-bold text-gray-900 leading-tight border-b border-transparent group-hover:border-black transition-all inline-block truncate uppercase tracking-wide">
                {{ $product->name }}
            </h3>
            <span class="text-[13px] font-bold text-gray-900 whitespace-nowrap">
                {{ number_format($product->base_price, 0, ',', '.') }}₫
            </span>
        </div>
        
        @if($product->description)
            <p class="text-[11px] text-gray-500 line-clamp-1 italic">
                {{ $product->description }}
            </p>
        @endif

        <!-- Color indicator placeholder -->
        <div class="pt-1">
            <div class="w-1.5 h-1.5 rounded-sm bg-black border border-white ring-1 ring-gray-100"></div>
        </div>
    </div>
</a>
