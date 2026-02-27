@extends('layouts.app')

@section('content')
<!-- Phần Hero -->
<div class="relative w-full h-[600px] md:h-[calc(100vh-64px)] overflow-hidden">
    <img src="{{ asset('user/img/hero-desktop.jpeg') }}" alt="Ảnh Hero" class="w-full h-full object-cover object-[30%_40%]">
    <div class="absolute top-1/2 md:top-1/2 left-[4%] md:left-[10%] -translate-y-1/2">
        <h1 class="text-3xl md:text-4xl text-black font-medium leading-tight mb-4">
            Thanh Lịch Tinh Tế,<br>
            Hài Hòa Với Thiên Nhiên
        </h1>
        <a href="{{ url('/new-arrivals') }}" class="inline-block bg-white text-black px-6 md:px-9 py-2 uppercase text-sm font-semibold tracking-wider hover:bg-gray-100 transition-colors">
            Hàng Mới
        </a>
    </div>
</div>

<!-- Sản Phẩm Bán Chạy -->
<div class="max-w-7xl mx-auto px-4 py-16">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl font-bold">Bán Chạy Nhất</h2>
        <a href="#" class="text-sm font-medium underline">Xem Tất Cả</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <!-- Danh sách 4 sản phẩm bán chạy -->

        <div class="group cursor-pointer">
            <div class="relative aspect-[3/4] overflow-hidden mb-4">
                <img src="{{ asset('user/img/ffdslfs.jpg') }}" alt="Sản phẩm 1" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.src='https://placehold.co/400x600?text=Product+1'">
            </div>
            <h3 class="text-sm font-medium text-gray-900 group-hover:underline">Váy Hoa Mùa Hè Cổ Chữ V</h3>
            <div class="flex items-center space-x-2 mt-1">
                <span class="text-sm">1.200.000₫</span>
            </div>
        </div>

        <div class="group cursor-pointer hidden md:block">
            <div class="relative aspect-[3/4] overflow-hidden mb-4">
                <img src="{{ asset('user/img/faw242q_ucbuq8.jpg') }}" alt="Sản phẩm 2" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.src='https://placehold.co/400x600?text=Product+2'">
            </div>
            <h3 class="text-sm font-medium text-gray-900 group-hover:underline">Áo Sơ Mi Cotton Thoáng Mát</h3>
            <div class="flex items-center space-x-2 mt-1">
                <span class="text-sm">850.000₫</span>
            </div>
        </div>

        <div class="group cursor-pointer hidden md:block">
            <div class="relative aspect-[3/4] overflow-hidden mb-4">
                <img src="{{ asset('user/img/ElbaDressOff-WhiteFreeTheLabel4.webp') }}" alt="Sản phẩm 3" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.src='https://placehold.co/400x600?text=Product+3'">
            </div>
            <h3 class="text-sm font-medium text-gray-900 group-hover:underline">Quần Linen Trắng Thanh Lịch</h3>
            <div class="flex items-center space-x-2 mt-1">
                <span class="text-sm">950.000₫</span>
            </div>
        </div>

        <div class="group cursor-pointer">
            <div class="relative aspect-[3/4] overflow-hidden mb-4">
                <img src="{{ asset('user/img/Wind-Down-Dress-Coconut-Half_1400x.webp') }}" alt="Sản phẩm 4" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.src='https://placehold.co/400x600?text=Product+4'">
            </div>
            <h3 class="text-sm font-medium text-gray-900 group-hover:underline">Váy Midi Dáng Dài Cột Eo</h3>
            <div class="flex items-center space-x-2 mt-1">
                <span class="text-sm">1.450.000₫</span>
            </div>
        </div>

    </div>
</div>

<!-- Bộ Sưu Tập -->
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-12">Bộ Sưu Tập</h1>

    <!-- Masonry Grid (Image 2 configuration) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-2 gap-y-4">

        <!-- Left Column -->
        <div class="flex flex-col gap-4">
            <!-- Áo Blouse -->
            <div class="relative group overflow-hidden">
                <img src="{{ asset('user/img/collection/Lifestyle_Detail_Something_Tailored_Shirt_White_1400x.webp') }}" alt="Áo Blouse" class="w-full h-[500px] object-cover object-top hover:scale-105 transition-transform duration-700">
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 w-48 text-center bg-white">
                    <a href="{{ url('/collection/tops') }}" class="block text-black py-2.5 text-xs font-medium tracking-wide">Áo Blouse</a>
                </div>
            </div>
            <!-- Váy Cao Cấp -->
            <div class="relative group overflow-hidden">
                <img src="{{ asset('user/img/collection/Save_The_Date_Dress_Khaki_Lifestyle_Khaki_Main_720x.webp') }}" alt="Váy Cao Cấp" class="w-full h-[700px] object-cover object-center hover:scale-105 transition-transform duration-700">
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 w-48 text-center bg-white">
                    <a href="{{ url('/collection/dresses-premium') }}" class="block text-black py-2.5 text-xs font-medium tracking-wide">Váy Cao Cấp</a>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="flex flex-col gap-4">
            <!-- Quần Âu -->
            <div class="relative group overflow-hidden">
                <img src="{{ asset('user/img/collection/Moodboard2_71ade389-dc80-49eb-b7e8-1c90a0273a2a_700x.webp') }}" alt="Quần Âu" class="w-full h-[800px] object-cover object-center hover:scale-105 transition-transform duration-700">
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 w-48 text-center bg-white">
                    <a href="{{ url('/collection/pants') }}" class="block text-black py-2.5 text-xs font-medium tracking-wide">Quần Âu</a>
                </div>
            </div>
            <!-- Áo Dạ -->
            <div class="relative group overflow-hidden">
                <img src="{{ asset('user/img/collection/ezgif-2-f137fd9d7d.png') }}" alt="Áo Dạ" class="w-full h-[400px] object-cover object-center hover:scale-105 transition-transform duration-700">
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 w-48 text-center bg-white">
                    <a href="{{ url('/collection/outwear') }}" class="block text-black py-2.5 text-xs font-medium tracking-wide">Áo Dạ</a>
                </div>
            </div>
        </div>

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
@endsection