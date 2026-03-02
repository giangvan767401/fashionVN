<x-app-layout>
<div class="font-[Inter] text-[#333333] pt-8 md:pt-16 pb-20">
    <div class="max-w-[1000px] px-4 md:px-8 mx-auto text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-bold uppercase tracking-wider mb-6">Sứ Mệnh Của Chúng Tôi</h1>
        <div class="w-16 h-1 bg-[#5c7a6b] mx-auto mb-8"></div>
        <p class="text-lg md:text-xl text-gray-600 leading-relaxed font-light">
            Tại Lumiere, chúng tôi tin rằng thời trang không chỉ là vẻ bề ngoài mà còn là cách bạn thể hiện bản thân, giá trị và thông điệp bạn muốn truyền tải tới thế giới. Sứ mệnh của chúng tôi là tạo ra những thiết kế vượt thời gian, kết hợp giữa phong cách hiện đại và sự tôn trọng dành cho con người, môi trường.
        </p>
    </div>

    <!-- Image section -->
    <div class="max-w-[1440px] px-4 md:px-8 mx-auto mb-20 relative">
        <div class="w-full h-[400px] md:h-[600px] overflow-hidden bg-gray-100 relative">
            <img src="{{ asset('user/img/Lifestyle.webp') }}" alt="Sứ Mệnh Lumiere" class="w-full h-full object-cover">
            <!-- Overlay Text -->
            <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                <h2 class="text-white text-3xl md:text-4xl font-bold tracking-widest uppercase text-center drop-shadow-lg px-4">Tôn Vinh Vẻ Đẹp Đích Thực</h2>
            </div>
        </div>
    </div>

    <!-- Core Values -->
    <div class="max-w-[1200px] px-4 md:px-8 mx-auto mb-20">
        <h2 class="text-2xl md:text-3xl font-bold uppercase tracking-wide text-center mb-12">Giá Trị Cốt Lõi</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
            <div class="flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-[#f3f3f3] flex items-center justify-center mb-6 text-[#5c7a6b]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Chất Lượng Hàng Đầu</h3>
                <p class="text-gray-600 leading-relaxed text-sm md:text-base">Mỗi sản phẩm đều được chăm chút tỉ mỉ từ khâu chọn chất liệu đến từng đường kim mũi chỉ, đảm bảo sự thoải mái và độ bền vượt trội.</p>
            </div>
            
            <div class="flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-[#f3f3f3] flex items-center justify-center mb-6 text-[#5c7a6b]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.115 5.19l.319 1.913A6 6 0 008.11 10.36L9.75 12l-.387.775c-.217.433-.132.956.21 1.298l1.348 1.348c.21.21.329.497.329.795v1.089c0 .426.24.815.622 1.006l.153.076c.433.217.956.132 1.298-.21l.723-.723a8.7 8.7 0 002.288-4.042 1.087 1.087 0 00-.358-1.099l-1.33-1.108c-.251-.21-.582-.299-.905-.245l-1.17.195a1.125 1.125 0 01-1.259-.711l-.228-.57a1.125 1.125 0 01.332-1.272l.48-.363c.27-.204.423-.526.423-.865v-.546c0-.52-.303-.984-.77-1.168l-1.125-.44a1.125 1.125 0 01-.659-1.01V5.5c0-.62-.5-1.125-1.125-1.125h-.54a1.125 1.125 0 01-1.125-1.125V3.115h.047a9.014 9.014 0 012.353-.115c.198.02.392.054.58.103z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Tôn Trọng Sự Đa Dạng</h3>
                <p class="text-gray-600 leading-relaxed text-sm md:text-base">Chúng tôi tin rằng mọi vóc dáng, màu da và phong cách cá nhân đều đáng được tôn vinh. Thời trang của chúng tôi là dành cho tất cả mọi người.</p>
            </div>
            
            <div class="flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-[#f3f3f3] flex items-center justify-center mb-6 text-[#5c7a6b]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Đổi Mới Sáng Tạo</h3>
                <p class="text-gray-600 leading-relaxed text-sm md:text-base">Luôn tìm kiếm những nguồn cảm hứng mới, bắt kịp xu hướng toàn cầu nhưng vẫn giữ vững nét tinh tế, thanh lịch đặc trưng.</p>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
