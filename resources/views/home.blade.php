<x-app-layout>
<!-- Phần Hero -->
<div class="relative w-full h-[600px] md:h-[calc(100vh-64px)] overflow-hidden">
    <img src="{{ asset('user/img/hero-desktop.jpeg') }}" alt="Ảnh Hero" class="w-full h-full object-cover object-[30%_40%]">
    <div class="absolute top-1/2 md:top-1/2 left-[4%] md:left-[10%] -translate-y-1/2">
        <h1 class="text-3xl md:text-4xl text-black font-medium leading-tight mb-4">
            Thanh Lịch Tinh Tế,<br>
            Hài Hòa Với Thiên Nhiên
        </h1>
        <a href="{{ route('collection') }}" class="inline-block bg-white text-black px-6 md:px-9 py-2 uppercase text-sm font-semibold tracking-wider hover:bg-gray-100 transition-colors">
            Hàng Mới
        </a>
    </div>
</div>

<!-- Sản Phẩm Bán Chạy -->
<div class="max-w-7xl mx-auto px-4 py-16">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl font-bold">Sản Phẩm Mới</h2>
        <a href="{{ route('collection') }}" class="text-sm font-medium underline">Xem Tất Cả</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-12">
        @php
            $bestSellers = \App\Models\Product::where('is_active', true)->with(['images', 'categories'])->latest()->take(4)->get();
        @endphp

        @foreach($bestSellers as $product)
            <x-product-card :product="$product" />
        @endforeach

        @if($bestSellers->isEmpty())
            <div class="col-span-full py-12 text-center text-gray-400 italic">
                Sản phẩm đang được cập nhật...
            </div>
        @endif
    </div>
</div>

<!-- Bộ Sưu Tập -->
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-4">
        <h2 class="text-2xl font-bold text-gray-900">Bộ Sưu Tập</h2>
    </div>

    <!-- Masonry-style Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        
        <!-- Cột Trái -->
        <div class="flex flex-col gap-4">
            
            <!-- Áo Blouse (Top Left) -->
            <div class="relative group cursor-pointer overflow-hidden aspect-[4/3] md:aspect-auto md:h-[45%]">
                <img src="{{ asset('user/img/collection/Lifestyle_Detail_Something_Tailored_Shirt_White_1400x.webp') }}" alt="Áo Blouse" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                <div class="absolute bottom-4 right-4 bg-white px-8 py-2 shadow-md">
                    <span class="text-[11px] font-semibold text-gray-800 tracking-widest uppercase">Áo Blouse</span>
                </div>
            </div>

            <!-- Váy Cao Cấp (Bottom Left) -->
            <div class="relative group cursor-pointer overflow-hidden aspect-[3/4] md:aspect-auto md:h-[55%]">
                <img src="{{ asset('user/img/collection/Save_The_Date_Dress_Khaki_Lifestyle_Khaki_Main_720x.webp') }}" alt="Váy Cao Cấp" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                <div class="absolute bottom-4 right-4 bg-white px-8 py-2 shadow-md">
                    <span class="text-[11px] font-semibold text-gray-800 tracking-widest uppercase">Váy Cao Cấp</span>
                </div>
            </div>

        </div>

        <!-- Cột Phải -->
        <div class="flex flex-col gap-4">
            
            <!-- Quần Âu (Top Right) -->
            <div class="relative group cursor-pointer overflow-hidden aspect-[3/4] md:aspect-auto md:h-[65%]">
                <img src="{{ asset('user/img/collection/P00831921_d1.avif') }}" alt="Quần Âu" class="w-full h-full object-cover object-top hover:scale-105 transition-transform duration-700">
                <div class="absolute bottom-4 left-4 bg-white px-8 py-2 shadow-md">
                    <span class="text-[11px] font-semibold text-gray-800 tracking-widest uppercase">Quần Âu</span>
                </div>
            </div>

            <!-- Áo Dạ (Bottom Right) -->
            <div class="relative group cursor-pointer overflow-hidden aspect-[4/3] md:aspect-auto md:h-[35%]">
                <img src="{{ asset('user/img/collection/Moodboard2_71ade389-dc80-49eb-b7e8-1c90a0273a2a_700x.webp') }}" alt="Áo Dạ" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                <div class="absolute bottom-4 right-4 bg-white px-8 py-2 shadow-md">
                    <span class="text-[11px] font-semibold text-gray-800 tracking-widest uppercase">Áo Dạ</span>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Khám phá sản phẩm (trước đây là Bộ Sưu Tập) -->
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-12">
        <h1 class="text-3xl font-bold">Khám phá sản phẩm</h1>
        <a href="{{ route('collection') }}" class="text-sm font-medium hover:underline flex items-center gap-2">
            Khám phá trọn bộ
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </a>
    </div>

    @php
        $mainCategories = \App\Models\Category::whereNull('parent_id')
            ->where('is_active', true)
            ->take(4)
            ->get();
    @endphp

    <div class="grid grid-cols-2 gap-4">
        @forelse($mainCategories as $category)
            @php
                $bgImage = $category->image_url 
                    ? (Str::startsWith($category->image_url, 'http') ? $category->image_url : asset('storage/' . $category->image_url))
                    : asset('user/img/collection/Lifestyle_Detail_Something_Tailored_Shirt_White_1400x.webp');
            @endphp
            <div class="relative group overflow-hidden rounded-sm">
                <div class="aspect-[3/4] md:aspect-[4/5] overflow-hidden">
                    <img src="{{ $bgImage }}" alt="{{ $category->name }}" class="w-full h-full object-cover object-top hover:scale-105 transition-transform duration-700">
                </div>
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 w-40 md:w-48 text-center bg-white shadow-xl translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                    <a href="{{ url('/collection?category=' . $category->id) }}" class="block text-black py-3 text-[10px] md:text-xs font-bold uppercase tracking-widest">{{ $category->name }}</a>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center border-2 border-dashed border-gray-100 rounded-3xl">
                <p class="text-gray-400 italic">Dữ liệu bộ sưu tập đang được cập nhật...</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Phong Cách Mỗi Ngày (Modiweek) -->
<div class="max-w-7xl mx-auto px-4 py-16">
    <h2 class="text-2xl font-bold mb-8">Phong Cách Mỗi Ngày</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
        @php
        $days = [
        ['name' => 'Thứ Hai', 'img' => '1.webp'],
        ['name' => 'Thứ Ba', 'img' => '2.webp'],
        ['name' => 'Thứ Tư', 'img' => '3.webp'],
        ['name' => 'Thứ Năm', 'img' => '4.webp'],
        ['name' => 'Thứ Sáu', 'img' => '5.webp'],
        ['name' => 'Thứ Bảy', 'img' => '6.webp'],
        ['name' => 'Chủ Nhật', 'img' => '7.webp'],
        ];
        @endphp

        @foreach (array_slice($days, 0, 5) as $day)
        <div class="group cursor-pointer">
            <div class="overflow-hidden aspect-[4/5] mb-3">
                <img src="{{ asset('user/img/modiweek/' . $day['img']) }}" alt="{{ $day['name'] }}" class="w-full h-full object-cover">
            </div>
            <h3 class="text-sm font-semibold text-gray-900">{{ $day['name'] }}</h3>
        </div>
        @endforeach
    </div>
</div>

<!-- Banner Sản Phẩm Xanh -->
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row items-stretch border border-gray-200">
        <div class="w-full md:w-1/2 overflow-hidden bg-[#e6e2df]">
            <img src="{{ asset('user/img/collection/Moodboard2_71ade389-dc80-49eb-b7e8-1c90a0273a2a_700x.webp') }}" alt="Sản Phẩm Xanh" class="w-full h-full object-cover mix-blend-multiply">
        </div>
        <div class="w-full md:w-1/2 flex flex-col justify-center items-start p-8 md:p-16 bg-[#e6e2df]">
            <h2 class="text-3xl font-bold mb-6 text-black">Trái tim hướng về sự bền vững.</h2>
            <p class="text-black mb-8 leading-relaxed max-w-md">Cam kết của chúng tôi với môi trường là luôn cẩn trọng lựa chọn chất liệu và ưu tiên quy trình sản xuất đạo đức. Chúng tôi tin rằng thời trang không chỉ giúp bạn mặc đẹp mà còn mang lại cảm giác an tâm về lựa chọn của mình.</p>
            <a href="{{ url('/sustainability') }}" class="inline-block border-b-2 border-black text-black font-semibold text-sm uppercase tracking-wider pb-1 hover:text-gray-600 hover:border-gray-600 transition-colors">
                Tìm Hiểu Thêm
            </a>
        </div>
    </div>
</div>

<!-- Theo Dõi Chúng Tôi -->
<div class="max-w-7xl mx-auto px-4 py-16 text-center">
    <h2 class="text-2xl font-bold mb-2">Theo Dõi Chúng Tôi</h2>
    <p class="text-gray-500 mb-8"><a href="#" class="underline">@Lumiere.vn</a></p>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @for ($i = 1; $i <= 4; $i++)
            <div class="relative group aspect-square overflow-hidden cursor-pointer">
            <img src="{{ asset('user/img/followus/' . $i . '.jpg') }}" alt="Instagram {{ $i }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">

            </div>
    </div>
    @endfor
</div>
</div>
</x-app-layout>