@extends('layouts.app')

@section('content')
<div class="relative w-full h-[calc(100vh-130px)] overflow-hidden">
    <!-- Hero Background Image -->
    <div class="absolute inset-0">
        <img src="{{ asset('home.jpg') }}" alt="Fashion Hero" class="w-full h-full object-cover object-center scale-105">
        <!-- Overlay Layer -->
        <div class="absolute inset-0 bg-black/10 backdrop-blur-[1px]"></div>
    </div>

    <!-- Content Overlay -->
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center">
        <div class="max-w-xl">
            <h1 class="text-5xl md:text-6xl font-bold text-[#1A1A1A] leading-[1.1] mb-8">
                Thanh Lịch Tinh Tế,<br>
                Hài Hòa Với Thiên Nhiên
            </h1>
            
            <a href="#" class="inline-block bg-white text-black px-12 py-4 text-sm font-medium rounded-full shadow-lg hover:bg-black hover:text-white transition-all duration-300 transform hover:-translate-y-1">
                Hàng Mới
            </a>
        </div>
    </div>
</div>
@endsection
