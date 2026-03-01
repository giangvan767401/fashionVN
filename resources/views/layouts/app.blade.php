<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Lumiere')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col pt-16">
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
                                    <li><a href="#" class="hover:text-black">Xem Tất Cả</a></li>
                                    <li><a href="#" class="hover:text-black">Blouses & Áo</a></li>
                                    <li><a href="#" class="hover:text-black">Quần Tây</a></li>
                                    <li><a href="#" class="hover:text-black">Váy & Jumpsuit</a></li>
                                    <li><a href="#" class="hover:text-black">Áo Khoác</a></li>
                                    <li><a href="#" class="hover:text-black">Áo Len</a></li>
                                    <li><a href="#" class="hover:text-black">Áo Thun</a></li>
                                    <li><a href="#" class="hover:text-black">Quần Short & Chân Váy</a></li>
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
                            <!-- Bộ Sản Phẩm Image -->
                            <div class="w-64">
                                <div class="relative aspect-[3/4] overflow-hidden mb-3">
                                    <img src="{{ asset('user/img/collection/Moodboard2_71ade389-dc80-49eb-b7e8-1c90a0273a2a_700x.webp') }}" alt="Bộ Sản Phẩm" class="w-full h-full object-cover">
                                </div>
                                <h3 class="text-sm font-medium">Bộ Sản Phẩm</h3>
                            </div>
                            
                            <!-- Kích Thước Image -->
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

            <!-- Hàng Mới with Mega Menu -->
            <div class="h-full flex items-center">
                <button type="button" id="megaMenuBtn2" onclick="document.getElementById('megaMenu2').classList.toggle('hidden')" class="hover:text-black py-4 h-full flex items-center">Hàng Mới</button>
                
                <!-- Mega Menu Dropdown -->
                <div id="megaMenu2" class="hidden absolute top-full left-0 w-full bg-white shadow-lg border-t border-gray-100 z-50">
                    <div class="max-w-7xl mx-auto px-8 py-12 flex justify-between">
                        
                        <!-- Columns 1-2: Links -->
                        <div class="flex space-x-16">
                            <!-- Danh Mục -->
                            <div>
                                <h3 class="font-medium text-black mb-6">Danh Mục</h3>
                                <ul class="space-y-4 text-[14px] text-gray-600">
                                    <li><a href="#" class="hover:text-black">Xem Tất Cả</a></li>
                                    <li><a href="#" class="hover:text-black">Blouses & Áo</a></li>
                                    <li><a href="#" class="hover:text-black">Áo Thun</a></li>
                                    <li><a href="#" class="hover:text-black">Quần Tây</a></li>
                                    <li><a href="#" class="hover:text-black">Áo Khoác</a></li>
                                    <li><a href="#" class="hover:text-black">Áo Len</a></li>
                                    <li><a href="#" class="hover:text-black">Váy & Jumpsuit</a></li>
                                    <li><a href="#" class="hover:text-black">Quần Short & Chân Váy</a></li>
                                </ul>
                            </div>
                            
                            <!-- Xu Hướng -->
                            <div>
                                <h3 class="font-medium text-black mb-6">Xu Hướng</h3>
                                <ul class="space-y-4 text-[14px] text-gray-600">
                                    <li><a href="#" class="hover:text-black">Kích Thước</a></li>
                                    <li><a href="#" class="hover:text-black">Bộ Sưu Tập Mùa Thu</a></li>
                                    <li><a href="#" class="hover:text-black">Modiweek</a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Columns 3-5: Images -->
                        <div class="flex space-x-6">
                            <!-- Bộ Sưu Tập Mùa Thu Image -->
                            <div class="w-64">
                                <div class="relative aspect-[1/2] overflow-hidden mb-3">
                                    <img src="{{ asset('user/img/Wind-Down-Dress-Coconut-Half_1400x.webp') }}" alt="Bộ Sưu Tập Mùa Thu" class="w-full h-full object-cover">
                                </div>
                                <h3 class="text-sm font-medium">Bộ Sưu Tập Mùa Thu</h3>
                            </div>
                            
                            <!-- Boluses Image -->
                            <div class="w-64">
                                <div class="relative aspect-[1/2] overflow-hidden mb-3">
                                    <img src="{{ asset('user/img/collection/Moodboard2_71ade389-dc80-49eb-b7e8-1c90a0273a2a_700x.webp') }}" alt="Boluses" class="w-full h-full object-cover">
                                </div>
                                <h3 class="text-sm font-medium">Boluses</h3>
                            </div>

                            <!-- Váy Image -->
                            <div class="w-64">
                                <div class="relative aspect-[1/2] overflow-hidden mb-3">
                                    <img src="{{ asset('user/img/MINI-DRESS-BEACH-0098-Edit_1400x.webp') }}" alt="Váy" class="w-full h-full object-cover">
                                </div>
                                <h3 class="text-sm font-medium">Váy</h3>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- Modiweek with Mega Menu -->
            <div class="h-full flex items-center">
                <button type="button" id="megaMenuBtn5" onclick="document.getElementById('megaMenu5').classList.toggle('hidden')" class="hover:text-black py-4 h-full flex items-center">Modiweek</button>
                
                <!-- Mega Menu Dropdown -->
                <div id="megaMenu5" class="hidden absolute top-full left-0 w-full bg-white shadow-lg border-t border-gray-100 z-50">
                    <div class="max-w-7xl mx-auto px-8 py-12 flex justify-between">
                        
                        <!-- Column 1: Links -->
                        <div class="flex space-x-16">
                            <!-- Phong Cách -->
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

                        <!-- Columns 2-4: Images -->
                        <div class="flex space-x-6">
                            <!-- Image 1 -->
                            <div class="w-64">
                                <div class="relative aspect-[4/5] overflow-hidden mb-3">
                                    <img src="{{ asset('user/img/modiweek/1.webp') }}" alt="Thứ Hai" class="w-full h-full object-cover">
                                </div>
                                <h3 class="text-sm font-medium">Thứ Hai Năng Động</h3>
                            </div>
                            
                            <!-- Image 2 -->
                            <div class="w-64">
                                <div class="relative aspect-[4/5] overflow-hidden mb-3">
                                    <img src="{{ asset('user/img/modiweek/3.webp') }}" alt="Thứ Tư" class="w-full h-full object-cover">
                                </div>
                                <h3 class="text-sm font-medium">Thứ Tư Thanh Lịch</h3>
                            </div>

                            <!-- Image 3 -->
                            <div class="w-64">
                                <div class="relative aspect-[4/5] overflow-hidden mb-3">
                                    <img src="{{ asset('user/img/modiweek/5.webp') }}" alt="Thứ Sáu" class="w-full h-full object-cover">
                                </div>
                                <h3 class="text-sm font-medium">Thứ Sáu Phá Cách</h3>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- Kích Thước with Mega Menu -->
            <div class="h-full flex items-center">
                <button type="button" id="megaMenuBtn3" onclick="document.getElementById('megaMenu3').classList.toggle('hidden')" class="hover:text-black py-4 h-full flex items-center">Kích Thước</button>
                
                <!-- Mega Menu Dropdown -->
                <div id="megaMenu3" class="hidden absolute top-full left-0 w-full bg-white shadow-lg border-t border-gray-100 z-50">
                    <div class="max-w-7xl mx-auto px-8 py-12 flex justify-between">
                        
                        <!-- Column 1: Links -->
                        <div class="flex space-x-16">
                            <!-- Danh Mục -->
                            <div>
                                <h3 class="font-medium text-black mb-6">Danh Mục</h3>
                                <ul class="space-y-4 text-[14px] text-gray-600">
                                    <li><a href="#" class="hover:text-black">Xem Tất Cả</a></li>
                                    <li><a href="#" class="hover:text-black">Áo & Blouse</a></li>
                                    <li><a href="#" class="hover:text-black">Áo Thun</a></li>
                                    <li><a href="#" class="hover:text-black">Quần Tây</a></li>
                                    <li><a href="#" class="hover:text-black">Áo Khoác</a></li>
                                    <li><a href="#" class="hover:text-black">Áo Len</a></li>
                                    <li><a href="#" class="hover:text-black">Váy & Jumpsuit</a></li>
                                    <li><a href="#" class="hover:text-black">Quần Short & Chân Váy</a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Columns 2-4: Images -->
                        <div class="flex space-x-6">
                            <!-- Quần Tây Image -->
                            <div class="w-64">
                                <div class="relative aspect-[1/2] overflow-hidden mb-3">
                                    <img src="{{ asset('user/img/hoverMenu/life-01.webp') }}" alt="Quần Tây" class="w-full h-full object-cover">
                                </div>
                                <h3 class="text-sm font-medium">Quần Tây</h3>
                            </div>
                            
                            <!-- Váy Image -->
                            <div class="w-64">
                                <div class="relative aspect-[1/2] overflow-hidden mb-3">
                                    <img src="{{ asset('user/img/ElbaDressOff-WhiteFreeTheLabel4.webp') }}" alt="Váy" class="w-full h-full object-cover">
                                </div>
                                <h3 class="text-sm font-medium">Váy</h3>
                            </div>

                            <!-- Blouses Image -->
                            <div class="w-64">
                                <div class="relative aspect-[1/2] overflow-hidden mb-3">
                                    <img src="{{ asset('user/img/Something-Borrowed-Shirt-Black-Half_1400x.webp') }}" alt="Blouses" class="w-full h-full object-cover">
                                </div>
                                <h3 class="text-sm font-medium">Blouses</h3>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- Sản Phẩm Xanh with Mega Menu -->
            <div class="h-full flex items-center">
                <button type="button" id="megaMenuBtn4" onclick="document.getElementById('megaMenu4').classList.toggle('hidden')" class="hover:text-black py-4 h-full flex items-center">Sản Phẩm Xanh</button>
                
                <!-- Mega Menu Dropdown -->
                <div id="megaMenu4" class="hidden absolute top-full left-0 w-full bg-white shadow-lg border-t border-gray-100 z-50">
                    <div class="max-w-7xl mx-auto px-8 py-12 flex justify-between">
                        
                        <!-- Column 1: Links -->
                        <div class="flex space-x-16">
                            <!-- Sản Phẩm Xanh -->
                            <div>
                                <h3 class="font-medium text-black mb-6">Sản Phẩm Xanh</h3>
                                <ul class="space-y-4 text-[14px] text-gray-600">
                                    <li><a href="{{ route('page.mission') }}" class="hover:text-black">Sứ Mệnh</a></li>
                                    <li><a href="{{ route('page.sustainability') }}" class="hover:text-black">Phát Triển Bền Vững</a></li>
                                    <li><a href="#" class="hover:text-black">Chất Liệu</a></li>
                                    <li><a href="#" class="hover:text-black">Bao Bì</a></li>
                                    <li><a href="#" class="hover:text-black">Chăm Sóc Sản Phẩm</a></li>
                                    <li><a href="#" class="hover:text-black">Nhà Cung Ứng</a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Columns 2-3: Images -->
                        <div class="flex space-x-6">
                            <!-- Image 1 -->
                            <div class="w-80">
                                <div class="relative aspect-square overflow-hidden mb-3">
                                    <img src="{{ asset('user/img/ElbaDressOff-WhiteFreeTheLabel4.webp') }}" alt="Váy Trắng" class="w-full h-full object-cover">
                                </div>
                            </div>
                            
                            <!-- Image 2 -->
                            <div class="w-80">
                                <div class="relative aspect-square overflow-hidden mb-3">
                                    <img src="{{ asset('user/img/Sustainability.png') }}" alt="Bền Vững" class="w-full h-full object-cover">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </nav>

        <div class="flex items-center space-x-6">
            <button aria-label="Search" class="hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>
            <button aria-label="Account" class="hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </button>
            <button aria-label="Wishlist" class="hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
            </button>
            <button aria-label="Cart" class="hover:text-gray-600">
                 <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
            </button>
        </div>
    </header>

    @if (Request::is('/'))
    <div class="bg-[#3D4B41] text-white text-xs w-full text-center py-2 absolute top-16 z-40">
        Nhận Ngay Dịch Vụ Giao Hàng Miễn Phí Cho Tất Cả Đơn Hàng
    </div>
    @endif

    <!-- Main Content -->
    <main class="flex-grow {{ Request::is('/') ? 'mt-8' : '' }}">
        @yield('content')
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
                    <li><a href="{{ route('collection') }}" class="hover:text-white">Bộ Sưu Tập</a></li>
                    <li><a href="{{ route('page.sustainability') }}" class="hover:text-white">Sản Phẩm Xanh</a></li>
                    <li><a href="{{ route('page.mission') }}" class="hover:text-white">Sứ Mệnh</a></li>
                    <li><a href="#" class="hover:text-white">Chính Sách Bảo Mật</a></li>
                    <li><a href="#" class="hover:text-white">Hệ Thống Hỗ Trợ</a></li>
                    <li><a href="#" class="hover:text-white">Điều Khoản & Điều Kiện</a></li>
                    <li><a href="#" class="hover:text-white">Thông Báo Bản Quyền</a></li>
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
                <a href="#" class="text-white hover:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                </a>
                <a href="#" class="text-white hover:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                </a>
                <a href="#" class="text-white hover:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.19 14.86c-.19.34-.63.45-.96.26-2.61-1.6-5.91-1.96-9.78-1.07-.38.09-.75-.15-.84-.53-.09-.38.15-.75.53-.84 4.25-.97 7.9-.57 10.79 1.21.34.19.45.62.26.96zm1.37-3.09c-.23.38-.72.5-1.11.27-2.98-1.83-7.55-2.39-10.74-1.31-.44.15-.92-.09-1.06-.53-.15-.44.09-.92.53-1.06 3.66-1.23 8.71-.6 12.11 1.5.39.23.51.72.27 1.11zm.07-3.21C14.05 8.42 8.16 8.21 4.76 9.24c-.54.16-1.12-.14-1.28-.68-.16-.54.14-1.12.68-1.28 3.91-1.19 10.43-.96 14.53 1.47.48.28.64.91.36 1.39-.28.48-.91.64-1.39.36z"/></svg>
                </a>
                <a href="#" class="text-white hover:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                </a>
            </div>
            <div class="text-xs text-gray-400">
                &copy; {{ date('Y') }} Lumiere. All Rights Reserved.
            </div>
        </div>
    </footer>

    <!-- Mega Menu Script -->
    <script>
        document.addEventListener('click', function(event) {
            const menu1 = document.getElementById('megaMenu');
            const btn1 = document.getElementById('megaMenuBtn');
            if (menu1 && !menu1.classList.contains('hidden')) {
                if (!menu1.contains(event.target) && !btn1.contains(event.target)) {
                    menu1.classList.add('hidden');
                }
            }

            const menu2 = document.getElementById('megaMenu2');
            const btn2 = document.getElementById('megaMenuBtn2');
            if (menu2 && !menu2.classList.contains('hidden')) {
                if (!menu2.contains(event.target) && !btn2.contains(event.target)) {
                    menu2.classList.add('hidden');
                }
            }

            const menu3 = document.getElementById('megaMenu3');
            const btn3 = document.getElementById('megaMenuBtn3');
            if (menu3 && !menu3.classList.contains('hidden')) {
                if (!menu3.contains(event.target) && !btn3.contains(event.target)) {
                    menu3.classList.add('hidden');
                }
            }

            const menu4 = document.getElementById('megaMenu4');
            const btn4 = document.getElementById('megaMenuBtn4');
            if (menu4 && !menu4.classList.contains('hidden')) {
                if (!menu4.contains(event.target) && !btn4.contains(event.target)) {
                    menu4.classList.add('hidden');
                }
            }

            const menu5 = document.getElementById('megaMenu5');
            const btn5 = document.getElementById('megaMenuBtn5');
            if (menu5 && !menu5.classList.contains('hidden')) {
                if (!menu5.contains(event.target) && !btn5.contains(event.target)) {
                    menu5.classList.add('hidden');
                }
            }
        });
    </script>
</body>
</html>
