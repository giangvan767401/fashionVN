<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Lumiere'))</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col pt-16">
    <!-- Fetch Cart Data -->
    @php
        $cart = null;
        if (Auth::check()) {
            $cart = \App\Models\Cart::where('user_id', Auth::id())->with(['items.variant.product.images', 'items.variant.attributeValues.group'])->first();
        } else {
            $sessionId = session()->getId();
            $cart = \App\Models\Cart::where('session_id', $sessionId)->with(['items.variant.product.images', 'items.variant.attributeValues.group'])->first();
        }
        $cartItems = $cart ? $cart->items : collect();
        $cartTotal = $cartItems->sum(function($item) { return $item->quantity * $item->unit_price; });
        $cartItemCount = $cartItems->sum('quantity');
    @endphp

    <!-- Navbar -->
    <header class="fixed top-0 left-0 right-0 h-16 bg-[#FDFBF7] shadow-sm z-50 flex items-center justify-between px-8">
        <div class="flex items-center">
            <a href="{{ url('/') }}" class="text-2xl font-bold tracking-tight">Lumiere</a>
            <span class="text-[10px] ml-1 mt-2 text-gray-500">women clothing</span>
        </div>
        
        <nav class="hidden md:flex justify-center space-x-8 text-[15px] font-medium text-gray-700 h-full">
            <!-- Bộ Sưu Tập with Mega Menu -->
            <div class="h-full flex items-center">
                <button type="button" id="megaMenuBtn" onclick="document.getElementById('megaMenu').classList.toggle('hidden')" class="hover:text-black py-4 h-full flex items-center">Bộ Sưu Tập</button>
                
                <!-- Mega Menu Dropdown -->
                <div id="megaMenu" class="hidden absolute top-full left-0 w-full bg-white shadow-lg border-t border-gray-100 z-50">
                    <div class="max-w-7xl mx-auto px-8 py-12 flex justify-between">
                        <!-- Columns 1-3: Links -->
                        <div class="flex space-x-16">
                            <!-- Danh Mục -->
                            <div>
                                <h3 class="font-medium text-black mb-6">Danh Mục</h3>
                                <ul class="space-y-4 text-[14px] text-gray-600">
                                    <li><a href="{{ route('collection') }}" class="hover:text-black">Xem Tất Cả</a></li>
                                    @php
                                        $mainCategories = \App\Models\Category::where('is_active', true)->whereNull('parent_id')->orderBy('sort_order')->get();
                                    @endphp
                                    @foreach($mainCategories as $cat)
                                        <li><a href="{{ route('collection') }}?category={{ $cat->id }}" class="hover:text-black">{{ $cat->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                            <!-- Nổi Bật -->
                            <div>
                                <h3 class="font-medium text-black mb-6">Nổi Bật</h3>
                                <ul class="space-y-4 text-[14px] text-gray-600">
                                    <li><a href="#" class="hover:text-black">Hàng Mới</a></li>
                                    <li><a href="#" class="hover:text-black">Modiweek</a></li>
                                    <li><a href="#" class="hover:text-black">Kích Thước</a></li>
                                    <li><a href="#" class="hover:text-black">Bán Chạy Nhất</a></li>
                                </ul>
                            </div>
                            <!-- Xem Thêm -->
                            <div>
                                <h3 class="font-medium text-black mb-6">Xem Thêm</h3>
                                <ul class="space-y-4 text-[14px] text-gray-600">
                                    <li><a href="#" class="hover:text-black">Bộ Sản Phẩm</a></li>
                                    <li><a href="#" class="hover:text-black">Trang Phục Dự Tiệc</a></li>
                                    <li><a href="#" class="hover:text-black">Bộ Đồ Đi Kèm</a></li>
                                    <li><a href="#" class="hover:text-black">Bộ Vest</a></li>
                                </ul>
                            </div>
                        </div>
                        <!-- Columns 4-5: Images -->
                        <div class="flex space-x-6">
                            <div class="w-64">
                                <div class="relative aspect-[3/4] overflow-hidden mb-3">
                                    <img src="{{ asset('user/img/collection/Moodboard2_71ade389-dc80-49eb-b7e8-1c90a0273a2a_700x.webp') }}" alt="Bộ Sản Phẩm" class="w-full h-full object-cover">
                                </div>
                                <h3 class="text-sm font-medium">Bộ Sản Phẩm</h3>
                            </div>
                            <div class="w-64">
                                <div class="relative aspect-[3/4] overflow-hidden mb-3 bg-[#EEEDEC]">
                                    <img src="{{ asset('user/img/collection/P00831921_d1.avif') }}" alt="Kích Thước" class="w-full h-full object-cover mix-blend-multiply">
                                </div>
                                <h3 class="text-sm font-medium">Kích Thước</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="h-full flex items-center">
                <button type="button" id="megaMenuBtn2" onclick="document.getElementById('megaMenu2').classList.toggle('hidden')" class="hover:text-black py-4 h-full flex items-center">Hàng Mới</button>
                <div id="megaMenu2" class="hidden absolute top-full left-0 w-full bg-white shadow-lg border-t border-gray-100 z-50">
                    <div class="max-w-7xl mx-auto px-8 py-12 flex justify-between">
                        <div class="flex space-x-16">
                            <div>
                                <h3 class="font-medium text-black mb-6">Danh Mục</h3>
                                <ul class="space-y-4 text-[14px] text-gray-600">
                                    <li><a href="{{ route('collection') }}" class="hover:text-black">Xem Tất Cả</a></li>
                                    <li><a href="#" class="hover:text-black">Blouses & Áo</a></li>
                                    <li><a href="#" class="hover:text-black">Áo Thun</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="flex space-x-6">
                            <div class="w-64">
                                <div class="relative aspect-[1/2] overflow-hidden mb-3">
                                    <img src="{{ asset('user/img/Wind-Down-Dress-Coconut-Half_1400x.webp') }}" alt="Bộ Sưu Tập" class="w-full h-full object-cover">
                                </div>
                                <h3 class="text-sm font-medium">Bộ Sưu Tập Mùa Thu</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="h-full flex items-center">
                <button type="button" id="megaMenuBtn5" onclick="document.getElementById('megaMenu5').classList.toggle('hidden')" class="hover:text-black py-4 h-full flex items-center">Modiweek</button>
                <!-- Mega Menu Dropdown -->
                <div id="megaMenu5" class="hidden absolute top-full left-0 w-full bg-white shadow-lg border-t border-gray-100 z-50">
                    <div class="max-w-7xl mx-auto px-8 py-12 flex justify-between">
                        <div class="flex space-x-16">
                            <div>
                                <h3 class="font-medium text-black mb-6">Phong Cách Mỗi Ngày</h3>
                                <ul class="space-y-4 text-[14px] text-gray-600">
                                    <li><a href="#" class="hover:text-black">Thứ Hai</a></li>
                                    <li><a href="#" class="hover:text-black">Thứ Ba</a></li>
                                    <li><a href="#" class="hover:text-black">Thứ Tư</a></li>
                                    <li><a href="#" class="hover:text-black">Thứ Năm</a></li>
                                    <li><a href="#" class="hover:text-black">Thứ Sáu</a></li>
                                    <li><a href="#" class="hover:text-black">Thứ Bảy</a></li>
                                    <li><a href="#" class="hover:text-black">Chủ Nhật</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="flex space-x-6">
                            <div class="w-64">
                                <div class="relative aspect-[4/5] overflow-hidden mb-3">
                                    <img src="{{ asset('user/img/modiweek/1.webp') }}" alt="Thứ Hai" class="w-full h-full object-cover">
                                </div>
                                <h3 class="text-sm font-medium">Thứ Hai Năng Động</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('collection') }}" class="hover:text-black py-4 flex items-center">Kích Thước</a>
            <a href="{{ route('page.sustainability') }}" class="hover:text-black py-4 flex items-center">Sản Phẩm Xanh</a>
        </nav>

        <!-- Search Overlay (Full Screen Premium) -->
        <div id="search-overlay" class="hidden fixed inset-0 z-[200]" style="background:rgba(255,255,255,0.97); backdrop-filter: blur(4px);">
            <div id="search-panel" class="w-full h-full flex flex-col" style="animation: slideSearchDown 0.25s ease forwards;">
                <!-- Header -->
                <div class="flex items-center justify-between px-8 md:px-16 pt-5 pb-5 border-b border-gray-100">
                    <a href="{{ url('/') }}" class="text-xl font-bold tracking-tight">Lumiere</a>
                    <button onclick="closeSearch()" class="text-gray-400 hover:text-black transition-colors p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>

                <!-- Search Input Big -->
                <div class="flex-1 flex flex-col items-center justify-start pt-16 px-6">
                    <div class="w-full max-w-2xl">
                        <form action="{{ route('collection') }}" method="GET" id="search-form">
                            <div class="flex items-center border-b-2 border-black pb-3 gap-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 shrink-0"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <input
                                    id="search-input"
                                    type="text"
                                    name="q"
                                    value="{{ request('q') }}"
                                    placeholder="Bạn đang tìm kiếm gì?"
                                    autocomplete="off"
                                    class="flex-1 text-2xl font-light bg-transparent border-none outline-none focus:ring-0 placeholder-gray-300 text-gray-800"
                                >
                                <button type="submit" class="shrink-0 text-gray-400 hover:text-black transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </button>
                            </div>
                        </form>

                        <!-- Quick Suggestions -->
                        <div class="mt-10">
                            <p class="text-xs font-semibold text-gray-400 tracking-widest uppercase mb-4">Tìm Kiếm Phổ Biến</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['Áo Blouse', 'Váy Midi', 'Quần Linen', 'Áo Khoác', 'Đầm Dạ', 'Cotton', 'Hàng Mới'] as $tag)
                                <a href="{{ route('collection') }}?q={{ urlencode($tag) }}"
                                   class="px-4 py-2 border border-gray-200 text-sm text-gray-600 hover:border-black hover:text-black transition-colors rounded-full">
                                    {{ $tag }}
                                </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Quick Category Links -->
                        <div class="mt-10 grid grid-cols-2 md:grid-cols-4 gap-4">
                            <a href="{{ route('collection') }}?collections[]=hang-moi" class="group flex flex-col items-center text-center gap-2">
                                <div class="w-full aspect-[4/3] overflow-hidden bg-gray-100">
                                    <img src="{{ asset('user/img/modiweek/1.webp') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Hàng Mới">
                                </div>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-black">Hàng Mới</span>
                            </a>
                            <a href="{{ route('collection') }}?collections[]=ban-chay-nhat" class="group flex flex-col items-center text-center gap-2">
                                <div class="w-full aspect-[4/3] overflow-hidden bg-gray-100">
                                    <img src="{{ asset('user/img/modiweek/2.webp') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Bán Chạy">
                                </div>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-black">Bán Chạy Nhất</span>
                            </a>
                            <a href="{{ route('collection') }}" class="group flex flex-col items-center text-center gap-2">
                                <div class="w-full aspect-[4/3] overflow-hidden bg-gray-100">
                                    <img src="{{ asset('user/img/modiweek/3.webp') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Tất Cả">
                                </div>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-black">Tất Cả Sản Phẩm</span>
                            </a>
                            <a href="{{ route('page.sustainability') }}" class="group flex flex-col items-center text-center gap-2">
                                <div class="w-full aspect-[4/3] overflow-hidden bg-gray-100">
                                    <img src="{{ asset('user/img/modiweek/4.webp') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Sản Phẩm Xanh">
                                </div>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-black">Sản Phẩm Xanh</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            @keyframes slideSearchDown {
                from { opacity: 0; transform: translateY(-12px); }
                to   { opacity: 1; transform: translateY(0); }
            }
        </style>


        <div class="flex items-center space-x-6">
            <button aria-label="Search" onclick="openSearch()" class="hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>
            
            @auth
                <div class="relative">
                    <button id="accountMenuBtn" onclick="toggleAccountMenu(event)" aria-label="Account" class="hover:text-gray-600 flex items-center focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <span class="ml-1 text-sm hidden lg:inline">{{ Auth::user()->full_name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div id="accountDropdown" class="absolute right-0 w-48 mt-2 py-2 bg-white rounded-md shadow-xl z-50 hidden border border-gray-100">
                        <div class="px-4 py-2 border-b border-gray-50 mb-1">
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Tài khoản</p>
                            <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            Hồ sơ
                        </a>
                        <a href="{{ route('profile.orders') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                            Đơn hàng của tôi
                        </a>
                        @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                            Trang quản trị
                        </a>
                        @endif
                        <div class="border-t border-gray-50 mt-1 pt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-black transition-colors">
                    Đăng nhập
                </a>
            @endauth

            <a href="{{ route('wishlist.index') }}" aria-label="Wishlist" class="hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
            </a>
            <button aria-label="Cart" class="hover:text-gray-600 relative flex items-center justify-center p-1" onclick="openCart()">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                @if($cartItemCount > 0)
                    <span class="absolute -top-1 -right-1 bg-[#D32F2F] text-white text-[10px] font-bold min-w-[16px] h-[16px] rounded-full flex items-center justify-center px-1" style="line-height: 1;">
                        {{ $cartItemCount }}
                    </span>
                @endif
            </button>
        </div>
    </header>

    @if (Request::is('/') || Request::is('register') || Request::is('login'))
    <div class="bg-[#3D4B41] text-white text-xs w-full text-center py-2 absolute top-16 z-40">
        Nhận Ngay Dịch Vụ Giao Hàng Miễn Phí Cho Tất Cả Đơn Hàng
    </div>
    @endif

    <!-- Main Content -->
    <main class="flex-grow {{ Request::is('/') || Request::is('register') || Request::is('login') ? 'mt-8' : '' }}">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-[#414141] text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-12">
            <div>
                <h3 class="font-bold mb-4">Tham Gia Câu Lạc Bộ Của Chúng Tôi & Nhận Ngay Ưu Đãi 15% Nhân Dịp Sinh Nhật</h3>
                <form class="mb-4">
                    <div class="flex border-b border-gray-500 pb-2">
                        <input type="email" placeholder="Nhập Địa Chỉ Email Của Bạn" class="bg-transparent w-full focus:outline-none text-sm placeholder-gray-400">
                        <button type="submit">→</button>
                    </div>
                </form>
                <div class="flex items-start space-x-2 text-xs text-gray-400">
                    <input type="checkbox" class="mt-1" id="newsletter_consent">
                    <label for="newsletter_consent">Bằng Việc Gửi Email Của Bạn, Bạn Đồng Ý Nhận Thông Tin Quảng Cáo Từ Lumiere</label>
                </div>
            </div>
            
            <div>
                <h3 class="font-bold mb-4">Giới Thiệu Về Lumiere</h3>
                <ul class="space-y-2 text-sm text-gray-300">
                    <li><a href="#" class="hover:text-white">Bộ Sưu Tập</a></li>
                    <li><a href="#" class="hover:text-white">Sản Phẩm Xanh</a></li>
                    <li><a href="#" class="hover:text-white">Chính Sách Bảo Mật</a></li>
                    <li><a href="#" class="hover:text-white">Hệ Thống Hỗ Trợ</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-bold mb-4">Trợ Giúp & Hỗ Trợ</h3>
                <ul class="space-y-2 text-sm text-gray-300">
                    <li><a href="#" class="hover:text-white">Vận Chuyển</a></li>
                    <li><a href="#" class="hover:text-white">Đổi Trả & Hoàn Tiền</a></li>
                    <li><a href="{{ route('page.faq') }}" class="hover:text-white">Hỏi & Đáp</a></li>
                    <li><a href="{{ route('page.contact') }}" class="hover:text-white">Liên Hệ</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-bold mb-4">Tham Gia Ngay</h3>
                <ul class="space-y-2 text-sm text-gray-300">
                    <li><a href="#" class="hover:text-white">Lumiere Club</a></li>
                    <li><a href="#" class="hover:text-white">Tuyển Dụng</a></li>
                    <li><a href="#" class="hover:text-white">Ghé Thăm</a></li>
                </ul>
            </div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 mt-16 flex flex-col md:flex-row justify-between items-center border-t border-gray-600 pt-8">
            <div class="flex space-x-4 mb-4 md:mb-0">
                <!-- Social links simplified -->
                <a href="#" class="text-white hover:text-gray-300">Instagram</a>
                <a href="#" class="text-white hover:text-gray-300">Twitter</a>
                <a href="#" class="text-white hover:text-gray-300">Spotify</a>
                <a href="#" class="text-white hover:text-gray-300">Youtube</a>
            </div>
            <div class="text-xs text-gray-400">
                &copy; {{ date('Y') }} Lumiere. All Rights Reserved.
            </div>
        </div>
    </footer>

    </footer>

    <!-- Mini Cart Drawer & Overlay -->
    <div id="cart-overlay" class="fixed inset-0 bg-black/50 z-[250] hidden opacity-0 transition-opacity duration-300 backdrop-blur-sm" onclick="closeCart(); return false;" style="cursor: pointer;"></div>
    
    <div id="cart-drawer" class="fixed inset-y-0 right-0 z-[300] bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col hidden" style="width: 400px; max-width: 100vw;">
        <!-- Cart Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <!-- Emtpy element for fine alignment if needed, or close button can be on the right -->
            <div></div> 
            <button onclick="closeCart()" class="text-gray-400 hover:text-black transition-colors p-2 absolute right-4 top-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <!-- Cart Body -->
        @if($cartItems->isEmpty())
            <div id="cart-body" class="flex-1 overflow-y-auto px-8 py-12 flex flex-col items-center justify-center text-center">
                <h3 class="text-[17px] font-bold text-gray-900 mb-4">Giỏ Hàng Của Bạn Đang Trống</h3>
                <p class="text-[13px] text-gray-600 mb-10 leading-relaxed px-4">
                    Khám Phá Lumiere Và Thêm Sản Phẩm Vào Giỏ Hàng Của Bạn
                </p>
                <style>
                    .cart-btn-green {
                        background-color: #61715b;
                        color: white;
                        transition: background-color 0.3s;
                    }
                    .cart-btn-green:hover {
                        background-color: #4a5845;
                    }
                </style>
                <a href="{{ route('collection') }}" class="cart-btn-green py-3 px-6 text-[13px] font-medium mb-4 block text-center" style="width: 85%;">
                    Bộ Sưu Tập
                </a>
                <a href="{{ route('collection') }}?collections[]=hang-moi" class="cart-btn-green py-3 px-6 text-[13px] font-medium block text-center" style="width: 85%;">
                    Hàng Mới
                </a>
            </div>
        @else
            <div id="cart-body" class="flex-1 overflow-y-auto p-6 flex flex-col space-y-6">
                @foreach($cartItems as $item)
                    @php
                        $product = $item->variant->product;
                        $primaryImage = $product->images->where('is_primary', true)->first();
                        $imageUrl = $primaryImage ? asset($primaryImage->url) : asset('user/img/default-product.jpg');
                        
                        $variantText = $item->variant_label;
                    @endphp
                    <div class="flex gap-4">
                        <!-- Image -->
                        <a href="{{ route('product.show', $product->slug) }}" class="flex-shrink-0 bg-gray-100 overflow-hidden block" style="width: 6rem; height: 120px;">
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        </a>
                        <!-- Info -->
                        <div class="flex-1 flex flex-col py-1">
                            <div class="flex justify-between items-start">
                                <a href="{{ route('product.show', $product->slug) }}" class="text-[14px] font-medium text-gray-900 line-clamp-2 pr-4 hover:underline">
                                    {{ $product->name }}
                                </a>
                                <!-- Remove button -->
                                <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500 mt-1" title="Xóa sản phẩm">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                    </button>
                                </form>
                            </div>
                            @if($variantText)
                                <p class="text-[11px] font-semibold text-[#D32F2F] mt-1 tracking-wider">{{ $variantText }}</p>
                            @endif
                            <div class="mt-auto flex justify-between items-end">
                                <!-- Quantity -->
                                <div class="flex items-center border border-gray-200 h-8">
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST" class="h-full">
                                        @csrf
                                        <input type="hidden" name="action" value="decrease">
                                        <button type="submit" class="px-2 text-gray-500 hover:text-black hover:bg-gray-50 h-full flex items-center justify-center focus:outline-none">-</button>
                                    </form>
                                    
                                    <input type="text" value="{{ $item->quantity }}" class="w-8 text-center text-[13px] border-none p-0 focus:ring-0 h-full" readonly>
                                    
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST" class="h-full">
                                        @csrf
                                        <input type="hidden" name="action" value="increase">
                                        <button type="submit" class="px-2 text-gray-500 hover:text-black hover:bg-gray-50 h-full flex items-center justify-center focus:outline-none">+</button>
                                    </form>
                                </div>
                                <!-- Price -->
                                <span class="text-[14px] font-medium">{{ number_format($item->unit_price, 0, ',', '.') }}đ</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Cart Footer -->
            <div class="border-t border-gray-100 p-6 bg-white">
                <style>
                    .cart-btn-green {
                        background-color: #61715b;
                        color: white;
                        transition: background-color 0.3s;
                    }
                    .cart-btn-green:hover {
                        background-color: #4a5845;
                    }
                </style>
                <div class="flex justify-between items-center mb-4">
                    <span class="text-[14px] font-medium text-gray-600">Tổng Tiền</span>
                    <span class="text-[18px] font-bold">{{ number_format($cartTotal, 0, ',', '.') }}đ</span>
                </div>
                <p class="text-[12px] text-gray-500 mb-6 text-center">Phí vận chuyển và thuế được tính ở trang thanh toán.</p>
                <a href="{{ route('cart.index') }}" class="cart-btn-green py-4 px-6 text-[14px] font-medium block w-full text-center">
                    Thanh Toán
                </a>
            </div>
        @endif
    </div>

    <!-- Scripts -->
    <script>
        // ... Click outside to close megamenus
        document.addEventListener('click', function(event) {
            const menuIds = ['megaMenu', 'megaMenu2', 'megaMenu5', 'accountDropdown'];
            const btnIds = ['megaMenuBtn', 'megaMenuBtn2', 'megaMenuBtn5', 'accountMenuBtn'];
            
            menuIds.forEach((mId, index) => {
                const menu = document.getElementById(mId);
                const btn = document.getElementById(btnIds[index]);
                if (menu && !menu.classList.contains('hidden')) {
                    if (!menu.contains(event.target) && !btn.contains(event.target)) {
                        menu.classList.add('hidden');
                    }
                }
            });
        });

        // Search Handlers
        function openSearch() {
            document.getElementById('search-overlay').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(() => document.getElementById('search-input').focus(), 100);
        }

        function closeSearch() {
            document.getElementById('search-overlay').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Cart Drawer Handlers
        function openCart() {
            const overlay = document.getElementById('cart-overlay');
            const drawer = document.getElementById('cart-drawer');
            
            overlay.classList.remove('hidden');
            drawer.classList.remove('hidden');
            
            // Timeout allows display:block to apply before animating opacity
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
            }, 10);
            
            setTimeout(() => {
                drawer.classList.remove('translate-x-full');
            }, 10);
            
            document.body.style.overflow = 'hidden';
            
            // TODO: In the future, fetch cart data via Fetch API and replace #cart-body content
        }

        function closeCart() {
            const overlay = document.getElementById('cart-overlay');
            const drawer = document.getElementById('cart-drawer');
            
            overlay.classList.add('opacity-0');
            drawer.classList.add('translate-x-full');
            
            setTimeout(() => {
                overlay.classList.add('hidden');
                drawer.classList.add('hidden');
            }, 300); // match transition duration-300
            
            document.body.style.overflow = '';
        }

        // Account Dropdown Handlers
        function toggleAccountMenu(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('accountDropdown');
            dropdown.classList.toggle('hidden');
        }

        function closeAccountMenu() {
            const dropdown = document.getElementById('accountDropdown');
            if (dropdown) dropdown.classList.add('hidden');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSearch();
                closeCart();
                closeAccountMenu();
            }
        });

        // Automatically open cart if the session flag is set (e.g. after adding item)
        @if(session('cart_drawer_open'))
            document.addEventListener('DOMContentLoaded', function() {
                openCart();
            });
        @endif
    </script>
    @if(session('login_success'))
    <!-- Welcome Modal -->
    <div
        x-data="{ open: true }"
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.5); backdrop-filter: blur(2px);"
        @click.self="open = false">

        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            style="background: white; border-radius: 4px; padding: 3.5rem 4rem; width: 600px; max-width: 90vw; position: relative; text-align: center; font-family: 'Montserrat', sans-serif;">

            <!-- Close Button -->
            <button @click="open = false"
                style="position: absolute; top: 16px; left: 20px; background: none; border: none; cursor: pointer; color: #9ca3af; line-height: 1; font-size: 20px; padding: 4px;">
                &times;
            </button>

            <!-- Title -->
            <h2 style="font-size: 26px; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0; letter-spacing: 0.01em;">
                Chào Mừng Đến Lumiere
            </h2>

            <!-- Italic Tagline -->
            <p style="font-size: 16px; font-style: italic; color: #6b7280; margin: 0 0 2rem 0; font-family: Georgia, serif; font-weight: 400;">
                "Thanh Lịch Trong Giản Đơn – Hòa Hợp Cùng Thiên Nhiên."
            </p>

            <!-- Question -->
            <p style="font-size: 17px; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0;">
                Bạn Mới Trải Nghiệm Lumiere Lần Đầu?
            </p>

            <!-- CTA Button -->
            <a href="{{ route('collection') }}"
                style="display: inline-block; padding: 13px 32px; background: #4a7c59; color: white; font-size: 14px; font-weight: 500; letter-spacing: 0.04em; border-radius: 3px; text-decoration: none;">
                Phong Cách Của Bạn, Do Bạn Tạo Nên
            </a>
        </div>
    </div>
    @endif
</body>
</html>
