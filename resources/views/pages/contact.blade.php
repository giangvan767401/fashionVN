@extends('layouts.app')

@section('content')
<div class="font-[Inter] text-[#333333] pt-8 md:pt-16 pb-20">
    <div class="max-w-[1200px] px-4 md:px-8 mx-auto">
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold uppercase tracking-wider mb-6">Liên Hệ Với Chúng Tôi</h1>
            <div class="w-16 h-1 bg-[#5c7a6b] mx-auto mb-8"></div>
            <p class="text-lg md:text-xl text-gray-600 leading-relaxed font-light max-w-2xl mx-auto">
                Dù bạn có thắc mắc về sản phẩm, đơn hàng hay chỉ muốn chia sẻ ý kiến, chúng tôi luôn sẵn sàng lắng nghe.
            </p>
        </div>

        <div class="flex flex-col md:flex-row gap-12 lg:gap-20">
            <!-- Contact Info -->
            <div class="w-full md:w-1/3">
                <h2 class="text-2xl font-bold mb-8 uppercase tracking-wide">Thông Tin Liên Hệ</h2>
                
                <div class="mb-8">
                    <h3 class="font-semibold text-lg mb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-[#5c7a6b]">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        Trụ Sở Chính
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Tầng 12, Tòa nhà Bitexco<br>
                        Số 2 Hải Triều, Q.1<br>
                        Thành phố Hồ Chí Minh, Việt Nam
                    </p>
                </div>

                <div class="mb-8">
                    <h3 class="font-semibold text-lg mb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-[#5c7a6b]">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.48-4.08-7.074-6.974l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                        Hotline Chăm Sóc Khách Hàng
                    </h3>
                    <p class="text-gray-600 leading-relaxed flex flex-col">
                        <a href="tel:19001234" class="hover:text-[#5c7a6b] transition-colors text-lg font-medium">1900 1234</a>
                        <span class="text-sm mt-1">Thứ 2 - Thứ 6: 9:00 - 18:00</span>
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-lg mb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-[#5c7a6b]">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        Email
                    </h3>
                    <a href="mailto:support@lumiere.com" class="text-gray-600 hover:text-[#5c7a6b] transition-colors underline underline-offset-4">support@lumiere.com</a>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="w-full md:w-2/3 bg-gray-50 p-8 md:p-12 border border-gray-100">
                <h2 class="text-2xl font-bold mb-8 uppercase tracking-wide">Gửi Tin Nhắn</h2>
                
                <form action="#" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Họ và Tên *</label>
                            <input type="text" id="name" name="name" required class="w-full border border-gray-300 px-4 py-3 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-colors" placeholder="Vd: Nguyễn Văn A">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email" id="email" name="email" required class="w-full border border-gray-300 px-4 py-3 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-colors" placeholder="Vd: example@gmail.com">
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Chủ đề *</label>
                        <select id="subject" name="subject" required class="w-full border border-gray-300 px-4 py-3 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-colors bg-white">
                            <option value="">Vui lòng chọn chủ đề</option>
                            <option value="order">Hỗ trợ Đơn hàng & Vận chuyển</option>
                            <option value="return">Đổi trả sản phẩm</option>
                            <option value="product">Tư vấn sản phẩm / Kích cỡ</option>
                            <option value="feedback">Góp ý dịch vụ</option>
                            <option value="other">Vấn đề khác</option>
                        </select>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Nội dung *</label>
                        <textarea id="message" name="message" rows="5" required class="w-full border border-gray-300 px-4 py-3 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-colors resize-y" placeholder="Nhập nội dung tin nhắn của bạn..."></textarea>
                    </div>

                    <button type="submit" class="w-full md:w-auto bg-[#333] text-white px-10 py-4 font-semibold uppercase tracking-wider hover:bg-black transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                        Gửi Yêu Cầu
                    </button>
                    <p class="text-xs text-gray-500 mt-4">* Các trường bắt buộc</p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
