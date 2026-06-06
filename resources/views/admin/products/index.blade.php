<x-admin-layout>
<div class="space-y-6">

    {{-- ═══ PAGE HEADER ═══ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-950 tracking-tight">Quản lý sản phẩm</h1>
            <p class="text-xs text-gray-500 mt-1">Xem, thêm, sửa, xóa và quản lý giá sale của sản phẩm thời trang.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="#"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-xs font-bold hover:bg-gray-50 transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Nhập Excel
            </a>
            <a href="{{ route('admin.products.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-[#24422e] text-white rounded-lg text-xs font-bold hover:bg-[#1b3223] transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Thêm sản phẩm
            </a>
        </div>
    </div>

    {{-- ═══ FLASH MESSAGE ═══ --}}
    @if (session('status'))
        <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-100 text-blue-800 rounded-xl text-xs font-semibold">
            <div class="w-6 h-6 rounded-md bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            {{ session('status') }}
        </div>
    @endif

    {{-- ═══ MAIN CONTENT CARD ═══ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
        
        {{-- Card Header & Search toolbar --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-base font-bold text-gray-900">Danh sách sản phẩm</h2>
            
            <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                {{-- Maintain filters across requests --}}
                <input type="hidden" name="tab" value="{{ request('tab', 'all') }}">
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">

                <div class="relative flex-1 sm:w-80">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Tìm theo tên, mã SKU, danh mục..." 
                           class="w-full pl-3 pr-10 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-[#24422e] focus:border-[#24422e] text-gray-700">
                    <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </button>
                </div>
                
                {{-- Toggle or Status filter --}}
                <a href="{{ request()->fullUrlWithQuery(['is_active' => request('is_active') == '1' ? '0' : '1']) }}"
                   class="inline-flex items-center justify-center gap-1.5 px-4 py-1.5 border border-gray-200 bg-white text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-50 transition-all shadow-sm whitespace-nowrap">
                    {{ request('is_active') == '1' ? 'Hiện sản phẩm ngừng bán' : 'Ẩn sản phẩm ngừng bán' }}
                </a>
            </form>
        </div>

        {{-- Filter Pills --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-gray-100 scrollbar-thin">
            @php
                $activeTab = request('tab', 'all');
                $categories = \App\Models\Category::all();
            @endphp
            
            {{-- All Tab --}}
            <a href="{{ route('admin.products.index', array_merge(request()->except(['page']), ['tab' => 'all'])) }}" 
               class="px-4 py-1.5 text-xs font-semibold rounded-full transition-all whitespace-nowrap {{ $activeTab == 'all' ? 'bg-[#24422e] text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
                Tất cả sản phẩm
            </a>
            
            {{-- Active Tab --}}
            <a href="{{ route('admin.products.index', array_merge(request()->except(['page']), ['tab' => 'active'])) }}" 
               class="px-4 py-1.5 text-xs font-semibold rounded-full transition-all whitespace-nowrap {{ $activeTab == 'active' ? 'bg-[#24422e] text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
                Đang bán
            </a>
            
            {{-- Sale Tab --}}
            <a href="{{ route('admin.products.index', array_merge(request()->except(['page']), ['tab' => 'sale'])) }}" 
               class="px-4 py-1.5 text-xs font-semibold rounded-full transition-all whitespace-nowrap {{ $activeTab == 'sale' ? 'bg-[#24422e] text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
                Đang giảm giá
            </a>
            
            {{-- Out of stock Tab --}}
            <a href="{{ route('admin.products.index', array_merge(request()->except(['page']), ['tab' => 'outofstock'])) }}" 
               class="px-4 py-1.5 text-xs font-semibold rounded-full transition-all whitespace-nowrap {{ $activeTab == 'outofstock' ? 'bg-[#24422e] text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
                Hết hàng
            </a>

            {{-- Separator --}}
            <div class="h-4 w-px bg-gray-200 mx-1"></div>

            {{-- Categories Tabs --}}
            @foreach($categories as $cat)
                <a href="{{ route('admin.products.index', array_merge(request()->except(['page']), ['tab' => 'cat_' . $cat->id])) }}" 
                   class="px-4 py-1.5 text-xs font-semibold rounded-full transition-all whitespace-nowrap {{ $activeTab == 'cat_' . $cat->id ? 'bg-[#24422e] text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        {{-- Date Filters --}}
        <form action="{{ route('admin.products.index') }}" method="GET" class="pb-2">
            {{-- Keep other parameters --}}
            <input type="hidden" name="tab" value="{{ request('tab', 'all') }}">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="is_active" value="{{ request('is_active') }}">

            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-600">
                <div class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-1.5 bg-white shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span>Từ</span>
                    <input type="date" 
                           name="start_date" 
                           value="{{ request('start_date') }}"
                           onchange="this.form.submit()"
                           class="border-0 p-0 focus:ring-0 focus:outline-none text-gray-700 w-28 text-xs cursor-pointer">
                </div>

                <div class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-1.5 bg-white shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span>đến</span>
                    <input type="date" 
                           name="end_date" 
                           value="{{ request('end_date') }}"
                           onchange="this.form.submit()"
                           class="border-0 p-0 focus:ring-0 focus:outline-none text-gray-700 w-28 text-xs cursor-pointer">
                </div>

                @if(request('start_date') || request('end_date'))
                    <a href="{{ route('admin.products.index', request()->except(['start_date', 'end_date', 'page'])) }}" 
                       class="text-rose-500 hover:text-rose-700 font-bold ml-2">
                        Xóa bộ lọc ngày
                    </a>
                @endif
            </div>
        </form>

        {{-- Table Grid --}}
        <div class="border border-gray-150 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left table-fixed min-w-[900px]">
                    <thead>
                        <tr class="bg-[#f4f7fc] border-b border-gray-200 text-gray-500">
                            <th class="w-[30%] px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-left border-r border-gray-150">Sản phẩm</th>
                            <th class="w-[15%] px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-left border-r border-gray-150">Danh mục</th>
                            <th class="w-[10%] px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-center border-r border-gray-150">Tồn kho</th>
                            <th class="w-[12%] px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-right border-r border-gray-150">Giá gốc</th>
                            <th class="w-[13%] px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-right border-r border-gray-150">Giá sale</th>
                            <th class="w-[10%] px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-center border-r border-gray-150">Trạng thái</th>
                            <th class="w-[20%] px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-150 bg-white">
                        @forelse ($products as $product)
                        @php
                            $primaryImg = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                            $imgUrl = '';
                            if ($primaryImg) {
                                $imgUrl = \Illuminate\Support\Str::startsWith($primaryImg->url, 'http')
                                    ? $primaryImg->url
                                    : (\Illuminate\Support\Str::startsWith($primaryImg->url, 'images/')
                                        ? asset($primaryImg->url)
                                        : asset('storage/' . $primaryImg->url));
                            }
                            $qty = $product->total_quantity;
                            $stockStatus = $qty === 0 ? 'empty' : ($qty <= 5 ? 'low' : 'ok');
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
    
                            {{-- Product info --}}
                            <td class="px-4 py-3 border-r border-gray-150 align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="relative w-10 h-12 rounded-lg overflow-hidden flex-shrink-0 bg-gray-50 border border-gray-100">
                                        @if($imgUrl)
                                            <img src="{{ $imgUrl }}" alt="{{ $product->name }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                            </div>
                                        @endif
                                        @if($product->discount_percent > 0)
                                            <div class="absolute top-0.5 left-0.5">
                                                <span class="inline-block px-1 py-0.5 bg-rose-500 text-white text-[7px] font-black rounded-sm leading-none">SALE</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                           class="text-xs font-bold text-gray-800 hover:text-[#24422e] transition-colors line-clamp-1 block leading-normal">
                                            {{ $product->name }}
                                        </a>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="text-[9px] text-gray-400 font-mono">{{ $product->sku ?? '—' }}</span>
                                            @if($product->created_at->diffInDays(now()) < 7)
                                                <span class="inline-flex items-center px-1 py-0.5 bg-blue-50 text-blue-600 text-[8px] font-bold rounded uppercase tracking-wider">Mới</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
    
                            {{-- Category --}}
                            <td class="px-4 py-3 border-r border-gray-150 align-middle">
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($product->categories as $category)
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[9px] font-bold uppercase tracking-wider">
                                            {{ $category->name }}
                                        </span>
                                    @empty
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endforelse
                                </div>
                            </td>
    
                            {{-- Stock --}}
                            <td class="px-4 py-3 text-center border-r border-gray-150 align-middle">
                                @if($stockStatus === 'empty')
                                    <span class="inline-block px-2 py-0.5 rounded bg-rose-50 text-rose-600 text-[10px] font-bold uppercase">Hết hàng</span>
                                @elseif($stockStatus === 'low')
                                    <div class="inline-flex flex-col items-center">
                                        <span class="text-xs font-bold text-amber-500">{{ $qty }}</span>
                                        <span class="text-[8px] font-medium text-amber-400">Sắp hết</span>
                                    </div>
                                @else
                                    <span class="text-xs font-medium text-gray-700">{{ $qty }}</span>
                                @endif
                            </td>
    
                            {{-- Base Price --}}
                            <td class="px-4 py-3 text-right font-semibold text-xs text-gray-700 border-r border-gray-150 align-middle">
                                {{ number_format($product->base_price, 0, ',', '.') }} đ
                            </td>
    
                            {{-- Sale Price --}}
                            <td class="px-4 py-3 text-right border-r border-gray-150 align-middle">
                                @if($product->discount_percent > 0)
                                    <div class="flex flex-col items-end gap-0.5">
                                        <span class="text-xs font-bold text-rose-600">{{ number_format($product->effective_price, 0, ',', '.') }} đ</span>
                                        <span class="inline-block px-1 py-0.5 bg-rose-100 text-rose-600 text-[8px] font-bold rounded-sm">-{{ $product->discount_percent }}%</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Không giảm</span>
                                @endif
                            </td>
    
                            {{-- Status toggle --}}
                            <td class="px-4 py-3 text-center border-r border-gray-150 align-middle">
                                <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="focus:outline-none" title="Nhấn để đổi trạng thái">
                                        @if ($product->is_active)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
                                                Đang bán
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors">
                                                Ngừng bán
                                            </span>
                                        @endif
                                    </button>
                                </form>
                            </td>
    
                            {{-- Actions --}}
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="inline-flex items-center justify-center px-3 py-1 bg-[#24422e] text-white rounded-md text-[10px] font-bold hover:bg-[#1b3223] transition-colors shadow-sm">
                                        Chỉnh sửa
                                    </a>
                                    
                                    {{-- View Icon Button --}}
                                    <a href="{{ route('home') }}#product-{{ $product->id }}" 
                                       target="_blank"
                                       class="w-6 h-6 inline-flex items-center justify-center text-gray-400 hover:text-gray-600 bg-white border border-gray-200 rounded-md hover:bg-gray-50 transition-colors"
                                       title="Xem chi tiết">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    
                                    {{-- Edit Icon Button --}}
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="w-6 h-6 inline-flex items-center justify-center text-gray-400 hover:text-[#24422e] bg-white border border-gray-200 rounded-md hover:bg-gray-50 transition-colors"
                                       title="Chỉnh sửa chi tiết">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                    </a>
                                    
                                    {{-- Delete Icon Button --}}
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                          class="inline confirm-form"
                                          data-confirm-text="Bạn có chắc muốn xóa '{{ $product->name }}'? Hành động này không thể hoàn tác.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-6 h-6 inline-flex items-center justify-center text-gray-400 hover:text-rose-600 bg-white border border-gray-200 rounded-md hover:bg-rose-50 transition-colors"
                                                title="Xóa sản phẩm">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                                    </div>
                                    <p class="text-xs font-bold text-gray-400">Không tìm thấy sản phẩm nào phù hợp</p>
                                    <a href="{{ route('admin.products.create') }}" class="text-xs text-[#24422e] font-bold hover:underline">+ Thêm sản phẩm mới</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($products->hasPages())
                <div class="px-6 py-4 border-t border-gray-150 bg-gray-50/30 flex items-center justify-between gap-4">
                    <p class="text-xs text-gray-400">
                        Hiển thị <span class="font-bold text-gray-700">{{ $products->firstItem() }}–{{ $products->lastItem() }}</span>
                        trong tổng số <span class="font-bold text-gray-700">{{ $products->total() }}</span> sản phẩm
                    </p>
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
</x-admin-layout>
