@extends('layouts.app')

@section('content')
<div class="font-[Inter] text-[#333333] pt-8 md:pt-16 pb-20">
    <div class="max-w-[1000px] px-4 md:px-8 mx-auto text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-bold uppercase tracking-wider mb-6">Phát Triển Bền Vững</h1>
        <div class="w-16 h-1 bg-[#5c7a6b] mx-auto mb-8"></div>
        <p class="text-lg md:text-xl text-gray-600 leading-relaxed font-light">
            Thời trang đẹp không nên đánh đổi bằng cái giá của Trái Đất. Chúng tôi cam kết thực hiện các bước đi ý nghĩa hướng tới một tương lai xanh hơn, thông qua các vật liệu hữu cơ, quy trình sản xuất đạo đức và giảm thiểu rác thải.
        </p>
    </div>

    <!-- Image section -->
    <div class="max-w-[1440px] px-4 md:px-8 mx-auto mb-20">
        <div class="w-full relative overflow-hidden bg-gray-50 flex items-center justify-center rounded-sm">
            <!-- Using the Sustainability.png provided by user -->
            <img src="{{ asset('user/img/Sustainability.png') }}" alt="Phát Triển Bền Vững" class="w-full h-auto object-cover md:max-h-[700px]">
        </div>
    </div>

    <!-- Initiatives -->
    <div class="bg-gray-50 py-20 mb-20">
        <div class="max-w-[1200px] px-4 md:px-8 mx-auto">
            <h2 class="text-2xl md:text-3xl font-bold uppercase tracking-wide text-center mb-16">Cam Kết Của Chúng Tôi</h2>
            
            <div class="flex flex-col md:flex-row gap-12 lg:gap-20 items-center mb-16">
                <div class="w-full md:w-1/2">
                    <h3 class="text-2xl font-bold mb-4 uppercase">Nguyên Liệu Hữu Cơ & Tái Chế</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Hơn 70% bộ sưu tập hiện tại của chúng tôi được làm từ bông hữu cơ nguyên chất, linen tự nhiên, và polyester tái chế. Các chất liệu này sử dụng ít nước hơn đáng kể và hoàn toàn không sử dụng hóa chất độc hại trong quá trình nuôi trồng cũng như xử lý.
                    </p>
                    <a href="{{ route('collection') }}" class="inline-flex items-center text-[#5c7a6b] font-medium hover:text-[#41564c] transition-colors underline underline-offset-4">Khám phá Sản Phẩm Xanh <span class="ml-2">&rarr;</span></a>
                </div>
                 <div class="w-full md:w-1/2 aspect-[4/3] bg-gray-200 overflow-hidden relative">
                    <img src="{{ asset('user/img/Bộ Sưu Tập/5.webp') }}" class="w-full h-full object-cover" alt="Organic Material">
                </div>
            </div>

            <div class="flex flex-col md:flex-row-reverse gap-12 lg:gap-20 items-center">
                <div class="w-full md:w-1/2">
                    <h3 class="text-2xl font-bold mb-4 uppercase">Quy Trình Sản Xuất Đạo Đức</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Chúng tôi hợp tác chặt chẽ với các nhà máy được cấp chứng chỉ công bằng xã hội. Từ việc đảm bảo mức lương xứng đáng, môi trường làm việc an toàn, cho đến việc nói không tuyệt đối với lao động trẻ em. Sự minh bạch trên toàn bộ chuỗi cung ứng là ưu tiên hàng đầu.
                    </p>
                </div>
                <div class="w-full md:w-1/2 aspect-[4/3] bg-gray-200 overflow-hidden relative">
                    <img src="{{ asset('user/img/Linen.webp') }}" onerror="this.src='{{ asset('user/img/Lifestyle.webp') }}'" class="w-full h-full object-cover" alt="Ethical Production">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
