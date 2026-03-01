<x-app-layout>
<div class="font-[Inter] text-[#333333] pt-8 md:pt-16 pb-20">
    <div class="max-w-[800px] px-4 md:px-8 mx-auto mb-16">
        <h1 class="text-4xl md:text-5xl font-bold uppercase tracking-wider mb-6 text-center">Câu Hỏi Thường Gặp</h1>
        <div class="w-16 h-1 bg-[#5c7a6b] mx-auto mb-8"></div>
        <p class="text-lg md:text-xl text-gray-600 leading-relaxed font-light text-center mb-12">
            Tìm câu trả lời cho các thắc mắc phổ biến nhất về mua sắm tại Lumiere. Nếu bạn cần hỗ trợ thêm, đừng ngần ngại liên hệ với chúng tôi.
        </p>

        <!-- FAQ Accordion -->
        <div class="border-t border-gray-200">
            
            <!-- Category 1: Đặt Hàng & Thanh Toán -->
            <h2 class="text-2xl font-bold mb-4 mt-8 uppercase tracking-wide">Đặt Hàng & Thanh Toán</h2>
            
            <div class="faq-item border-b border-gray-200">
                <button class="w-full flex justify-between items-center py-5 font-medium text-left faq-btn text-lg hover:text-[#5c7a6b] transition-colors">
                    Làm thế nào để tôi có thể đặt hàng?
                    <span class="text-2xl leading-none font-light toggle-icon">+</span>
                </button>
                <div class="faq-content hidden pb-6 text-gray-600 leading-relaxed">
                    Bạn chỉ cần chọn sản phẩm yêu thích, thêm vào giỏ hàng và tiến hành thanh toán theo các bước hướng dẫn trên màn hình. Chúng tôi hỗ trợ thanh toán khi nhận hàng (COD) và thanh toán qua thẻ tín dụng/thẻ ghi nợ.
                </div>
            </div>

            <div class="faq-item border-b border-gray-200">
                <button class="w-full flex justify-between items-center py-5 font-medium text-left faq-btn text-lg hover:text-[#5c7a6b] transition-colors">
                    Lumiere chấp nhận những hình thức thanh toán nào?
                    <span class="text-2xl leading-none font-light toggle-icon">+</span>
                </button>
                <div class="faq-content hidden pb-6 text-gray-600 leading-relaxed">
                    Hiện tại chúng tôi chấp nhận thanh toán qua thẻ tín dụng (Visa, MasterCard), ví điện tử (MoMo, ZaloPay), chuyển khoản ngân hàng và Thanh toán khi nhận hàng (COD).
                </div>
            </div>

            <div class="faq-item border-b border-gray-200">
                <button class="w-full flex justify-between items-center py-5 font-medium text-left faq-btn text-lg hover:text-[#5c7a6b] transition-colors">
                    Làm sao để tôi áp dụng mã giảm giá?
                    <span class="text-2xl leading-none font-light toggle-icon">+</span>
                </button>
                <div class="faq-content hidden pb-6 text-gray-600 leading-relaxed">
                    Tại trang thanh toán, bạn sẽ thấy ô "Mã giảm giá/Khuyến mãi". Nhập mã của bạn vào đó và nhấn "Áp dụng" để tận hưởng ưu đãi trước khi hoàn tất đơn hàng.
                </div>
            </div>

            <!-- Category 2: Giao Hàng & Đổi Trả -->
            <h2 class="text-2xl font-bold mb-4 mt-12 uppercase tracking-wide">Giao Hàng & Đổi Trả</h2>

            <div class="faq-item border-b border-gray-200">
                <button class="w-full flex justify-between items-center py-5 font-medium text-left faq-btn text-lg hover:text-[#5c7a6b] transition-colors">
                    Thời gian giao hàng là bao lâu?
                    <span class="text-2xl leading-none font-light toggle-icon">+</span>
                </button>
                <div class="faq-content hidden pb-6 text-gray-600 leading-relaxed">
                    Đối với khu vực nội thành, thời gian giao hàng dự kiến từ 1-2 ngày làm việc. Với khu vực ngoại thành và các tỉnh lẻ, thời gian từ 3-5 ngày làm việc.
                </div>
            </div>

            <div class="faq-item border-b border-gray-200">
                <button class="w-full flex justify-between items-center py-5 font-medium text-left faq-btn text-lg hover:text-[#5c7a6b] transition-colors">
                    Chính sách đổi trả của Lumiere như thế nào?
                    <span class="text-2xl leading-none font-light toggle-icon">+</span>
                </button>
                <div class="faq-content hidden pb-6 text-gray-600 leading-relaxed">
                    Chúng tôi hỗ trợ đổi/trả sản phẩm trong vòng 14 ngày kể từ ngày nhận hàng với điều kiện sản phẩm còn nguyên tag, chưa qua sử dụng và không bị các lỗi do khách hàng gây ra.
                </div>
            </div>
            
        </div>
        
        <!-- CTA -->
        <div class="text-center mt-12">
            <p class="text-gray-600 mb-6">Bạn vẫn còn câu hỏi khác?</p>
            <a href="{{ route('page.contact') }}" class="inline-block bg-[#333] text-white px-8 py-4 font-semibold uppercase tracking-wider hover:bg-black transition-colors">
                Liên Hệ Hỗ Trợ
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const faqBtns = document.querySelectorAll('.faq-btn');
        
        faqBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const content = this.nextElementSibling;
                const icon = this.querySelector('.toggle-icon');
                
                // Toggle current accordion
                if (content.classList.contains('hidden')) {
                    content.classList.remove('hidden');
                    icon.textContent = '−';
                } else {
                    content.classList.add('hidden');
                    icon.textContent = '+';
                }
            });
        });
    });
</script>
</x-app-layout>
