<x-app-layout>
<div class="font-[Montserrat] bg-[#FDFBF7] min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <!-- Breadcrumb / Back Link -->
        <div class="mb-8 flex justify-between items-center">
            <a href="{{ route('tryon.index', $product->slug) }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-black transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('Thử lại ảnh khác') }}
            </a>
            
            <a href="{{ route('product.show', $product->slug) }}" class="text-sm font-medium text-[#5c7a6b] hover:text-[#4a6356] transition-colors">
                {{ __('Quay lại trang sản phẩm') }} →
            </a>
        </div>

        <!-- Main Card Grid -->
        <div class="bg-white border border-gray-100 rounded-3xl overflow-hidden shadow-sm grid grid-cols-1 md:grid-cols-12 gap-0">
            
            <!-- Left Side: Result Image Container (Md: 7/12) -->
            <div class="md:col-span-7 bg-gray-50 p-6 sm:p-8 flex flex-col justify-center items-center relative min-h-[450px]">
                
                @if(isset($isMock) && $isMock)
                    <!-- Demo Mode Notice Overlay -->
                    <div class="absolute top-4 left-4 right-4 z-10 bg-amber-50/95 backdrop-blur-md border border-amber-100 px-4 py-2.5 rounded-2xl shadow-sm text-xs text-amber-800 flex items-start gap-2">
                        <span class="text-base leading-none">💡</span>
                        <div>
                            <span class="font-bold">Chế độ Demo (Chưa cấu hình API):</span> Thêm token Hugging Face vào <code>HF_API_TOKEN</code> trong tệp <code>.env</code> để kích hoạt ghép đồ AI thật bằng mô hình <strong>IDM-VTON</strong>.
                        </div>
                    </div>

                    <!-- Demo Mode Side-by-Side Comparison -->
                    <div class="flex flex-col sm:flex-row items-center gap-4 w-full justify-center py-6 mt-8">
                        <!-- User Photo -->
                        <div class="relative w-full max-w-[170px] aspect-[3/4] rounded-2xl overflow-hidden shadow-md border border-gray-200 bg-white">
                            <img src="{{ $resultUrl }}" alt="Ảnh của bạn" class="w-full h-full object-cover">
                            <div class="absolute bottom-2 left-2 right-2 bg-black/60 backdrop-blur-sm text-white text-[9px] py-1 text-center rounded-lg font-medium">
                                👤 Ảnh của bạn
                            </div>
                        </div>

                        <!-- Match Arrow / Magic Sparkles -->
                        <div class="flex flex-col items-center justify-center text-[#5c7a6b] font-bold p-2 shrink-0">
                            <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l-4-4"></path>
                            </svg>
                            <span class="text-[8px] uppercase tracking-widest mt-1.5 font-bold">Thử đồ ảo AI</span>
                        </div>

                        <!-- Garment Image -->
                        <div class="relative w-full max-w-[170px] aspect-[3/4] rounded-2xl overflow-hidden shadow-md border border-gray-200 bg-white">
                            <img src="{{ $garmentUrl }}" alt="Trang phục" class="w-full h-full object-cover">
                            <div class="absolute bottom-2 left-2 right-2 bg-[#5c7a6b]/90 backdrop-blur-sm text-white text-[9px] py-1 text-center rounded-lg font-medium">
                                👗 Trang phục thử
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Image Showcase Frame -->
                    <div class="w-full max-w-sm aspect-[3/4] rounded-2xl overflow-hidden shadow-md bg-white border border-gray-100 relative group">
                        <img id="resultImage" src="{{ $resultUrl }}" alt="Kết quả thử đồ ảo AI" class="w-full h-full object-cover">
                        
                        <!-- Action Overlay for Image -->
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-4">
                            <a 
                                href="{{ $resultUrl }}" 
                                download="tryon-result-{{ $product->slug }}.jpg" 
                                target="_blank"
                                class="w-12 h-12 rounded-full bg-white text-gray-800 flex items-center justify-center shadow-lg hover:scale-110 transition-transform duration-200"
                                title="Tải ảnh về máy"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                            </a>
                            <button 
                                onclick="zoomImage()" 
                                class="w-12 h-12 rounded-full bg-white text-gray-800 flex items-center justify-center shadow-lg hover:scale-110 transition-transform duration-200"
                                title="Xem kích thước lớn"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Side: Details & Actions (Md: 5/12) -->
            <div class="md:col-span-5 p-8 flex flex-col justify-between bg-white border-t md:border-t-0 md:border-l border-gray-100">
                <div class="flex flex-col gap-6">
                    <div>
                        <span class="text-[10px] font-bold tracking-widest text-[#5c7a6b] uppercase block mb-1">KẾT QUẢ THỬ ĐỒ VỚI AI</span>
                        <h2 class="text-2xl font-bold text-gray-900 leading-snug">{{ $product->name }}</h2>
                        
                        <div class="flex items-center gap-4 mt-2">
                            <span class="text-lg font-bold text-gray-900">
                                {{ number_format($product->sale_price ?? $product->base_price, 0, ',', '.') }}₫
                            </span>
                            <span class="text-xs text-gray-400 font-mono">
                                {{ $product->sku ?? ('ID: ' . $product->id) }}
                            </span>
                        </div>
                    </div>

                    <!-- Details Accordion / Text -->
                    <div class="border-t border-gray-100 pt-5">
                        <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Trạng thái xử lý</h3>
                        
                        @if(isset($isMock) && $isMock)
                            <div class="bg-amber-50/60 border border-amber-100 rounded-2xl p-4 text-[11px] text-amber-900 leading-relaxed flex flex-col gap-2">
                                <p class="font-bold text-amber-800">⚠️ Tại sao ảnh chưa được ghép đồ thật?</p>
                                <p>Bạn chưa cấu hình token Hugging Face. Hệ thống đang chạy ở chế độ demo.</p>
                                
                                <div class="border-t border-amber-200/50 pt-2 mt-1">
                                    <p class="font-bold text-amber-800 mb-1">💡 Cách kích hoạt AI thật (IDM-VTON):</p>
                                    <ol class="list-decimal pl-4 space-y-1 text-gray-700">
                                        <li>Đăng nhập và lấy token tại <a href="https://huggingface.co/settings/tokens" target="_blank" class="underline font-bold text-[#5c7a6b]">huggingface.co/settings/tokens</a></li>
                                        <li>Mở tệp <code>.env</code> trong thư mục gốc dự án</li>
                                        <li>Thêm dòng: <code class="bg-amber-100 px-1 rounded">HF_API_TOKEN=hf_xxxxxxxxxxxx</code></li>
                                        <li>Chạy lệnh <code>php artisan config:clear</code> rồi thử lại</li>
                                    </ol>
                                </div>
                            </div>
                        @else
                            <p class="text-xs text-gray-600 leading-relaxed">
                                Mô hình <strong>IDM-VTON</strong> (Hugging Face) tự động căn chỉnh tỷ lệ vai, eo và hông để quần áo ôm sát dáng người mẫu một cách tự nhiên nhất.
                            </p>
                        @endif
                    </div>

                    @if($product->variants && $product->variants->sum('quantity') > 0)
                        <!-- Stock Status -->
                        <div class="flex items-center gap-2 text-xs font-semibold text-[#5c7a6b]">
                            <span class="w-2 h-2 rounded-full bg-[#5c7a6b] inline-block"></span>
                            Sản phẩm đang có sẵn hàng tại kho hàng của Lumiere.
                        </div>
                    @endif
                </div>

                <!-- Actions List -->
                <div class="flex flex-col gap-3 mt-8 md:mt-0">
                    <a 
                        href="{{ route('product.show', $product->slug) }}" 
                        class="w-full bg-[#333] hover:bg-black text-white text-center py-4 px-6 rounded-2xl font-bold uppercase tracking-wider transition-colors shadow-sm block focus:outline-none"
                    >
                        Mua Ngay Sản Phẩm
                    </a>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <a 
                            href="{{ route('tryon.index', $product->slug) }}" 
                            class="w-full border border-gray-300 hover:border-black bg-white text-gray-800 text-center py-3.5 px-4 rounded-2xl font-semibold text-sm transition-colors block focus:outline-none"
                        >
                            Thử ảnh khác
                        </a>
                        <a 
                            href="{{ $resultUrl }}" 
                            download="tryon-result-{{ $product->slug }}.jpg" 
                            target="_blank"
                            class="w-full border border-gray-300 hover:border-black bg-white text-gray-800 text-center py-3.5 px-4 rounded-2xl font-semibold text-sm transition-colors flex items-center justify-center gap-1.5 focus:outline-none"
                        >
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Tải về
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Zoom Modal -->
<div id="zoomModal" class="fixed inset-0 bg-black/95 z-[99999] flex items-center justify-center p-4 hidden opacity-0 transition-opacity duration-300" onclick="closeZoom()">
    <button onclick="closeZoom()" class="absolute top-4 right-4 text-white hover:text-gray-300 p-3 focus:outline-none">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
    <img id="zoomedImage" src="{{ $resultUrl }}" alt="Zoomed Try-On result" class="max-h-[90vh] max-w-[90vw] object-contain rounded-lg shadow-2xl">
</div>

<script>
    function zoomImage() {
        const modal = document.getElementById('zoomModal');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
        }, 50);
    }

    function closeZoom() {
        const modal = document.getElementById('zoomModal');
        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>
</x-app-layout>
