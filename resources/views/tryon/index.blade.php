<x-app-layout>
<div class="font-[Montserrat] bg-[#FDFBF7] min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <!-- Breadcrumb & Back Link -->
        <div class="mb-8">
            <a href="{{ route('product.show', $product->slug) }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-black transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('Quay lại chi tiết sản phẩm') }}
            </a>
        </div>

        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-3">
                ✨ Virtual Try-On <span class="font-light">| Thử Đồ Ảo AI</span>
            </h1>
            <p class="text-base text-gray-600 max-w-2xl mx-auto">
                Tải ảnh chân dung của bạn lên để ướm thử trang phục này ngay lập tức bằng công nghệ trí tuệ nhân tạo <strong>IDM-VTON</strong> từ Hugging Face.
            </p>
        </div>

        @if(session('error'))
            <div class="mb-8 p-4 bg-rose-50 border border-rose-100 rounded-2xl text-rose-700 text-sm font-medium flex items-center gap-3 max-w-3xl mx-auto shadow-sm">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Column: Product Info (Lg: 4/12) -->
            <div class="lg:col-span-5 bg-white border border-gray-100 rounded-3xl p-6 shadow-sm flex flex-col gap-6">
                <div class="aspect-[3/4] rounded-2xl overflow-hidden bg-gray-50 relative group">
                    <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    <div class="absolute bottom-4 left-4 bg-black/60 backdrop-blur-md text-white text-[11px] px-3 py-1 rounded-full font-medium tracking-wide">
                        {{ $product->sku ?? ('ID: ' . $product->id) }}
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <h2 class="text-xl font-bold text-gray-900 leading-tight">{{ $product->name }}</h2>
                    <div class="text-lg font-semibold text-[#5c7a6b]">
                        {{ number_format($product->sale_price ?? $product->base_price, 0, ',', '.') }}₫
                    </div>
                    @if($product->short_desc)
                        <p class="text-xs text-gray-500 leading-relaxed mt-1">
                            {{ $product->short_desc }}
                        </p>
                    @endif
                </div>

                <!-- Category Configuration -->
                <div class="border-t border-gray-100 pt-6">
                    <label for="category" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Phân loại trang phục
                    </label>
                    <div class="relative">
                        <select name="category" id="category_select" form="tryonForm" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#5c7a6b] focus:border-[#5c7a6b] transition-all appearance-none cursor-pointer">
                            <option value="tops" {{ $detectedCategory === 'tops' ? 'selected' : '' }}>Áo / Áo khoác (Tops)</option>
                            <option value="bottoms" {{ $detectedCategory === 'bottoms' ? 'selected' : '' }}>Quần / Váy chân (Bottoms)</option>
                            <option value="one-pieces" {{ $detectedCategory === 'one-pieces' ? 'selected' : '' }}>Đầm / Váy liền / Jumpsuit (One-pieces)</option>
                        </select>
                        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-2 leading-relaxed">
                        * Tự động nhận diện dựa trên thuộc tính sản phẩm. Bạn có thể thay đổi nếu nhận diện chưa đúng.
                    </p>
                </div>
            </div>

            <!-- Right Column: Upload Area & Action (Lg: 7/12) -->
            <div class="lg:col-span-7 bg-white border border-gray-100 rounded-3xl p-6 sm:p-8 shadow-sm">
                <form action="{{ route('tryon.process') }}" method="POST" enctype="multipart/form-data" id="tryonForm">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <!-- Hidden category field populated by select outside form -->
                    <input type="hidden" name="category" id="hidden_category" value="{{ $detectedCategory }}">

                    <h3 class="text-lg font-bold text-gray-900 mb-5">Tải ảnh của bạn</h3>

                    <!-- Upload Box -->
                    <div 
                        id="uploadArea" 
                        class="border-2 border-dashed border-gray-200 hover:border-[#5c7a6b] bg-gray-50/50 hover:bg-[#5c7a6b]/5 rounded-2xl p-8 text-center cursor-pointer transition-all duration-300 relative overflow-hidden group min-h-[300px] flex flex-col justify-center items-center"
                        onclick="document.getElementById('userPhoto').click()"
                    >
                        <input type="file" name="user_photo" id="userPhoto" accept="image/*" class="hidden" required>

                        <!-- Empty State Content -->
                        <div id="uploadPlaceholder" class="flex flex-col items-center justify-center gap-4 transition-all duration-300">
                            <div class="w-16 h-16 rounded-full bg-white shadow-sm flex items-center justify-center text-gray-400 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8 text-[#5c7a6b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-semibold text-gray-800">Kéo thả ảnh hoặc click để chọn</span>
                                <span class="text-xs text-gray-500">Hỗ trợ các định dạng JPG, PNG, WEBP tối đa 5MB</span>
                            </div>
                            
                            <!-- Advice Badges -->
                            <div class="grid grid-cols-2 gap-2 mt-4 max-w-md mx-auto">
                                <div class="bg-white border border-gray-100 rounded-xl p-2.5 text-left flex items-start gap-2">
                                    <span class="text-emerald-500 text-xs">✓</span>
                                    <p class="text-[10px] leading-tight text-gray-500">Ảnh chụp thẳng, đủ ánh sáng, rõ mặt.</p>
                                </div>
                                <div class="bg-white border border-gray-100 rounded-xl p-2.5 text-left flex items-start gap-2">
                                    <span class="text-emerald-500 text-xs">✓</span>
                                    <p class="text-[10px] leading-tight text-gray-500">Nền đơn giản, trang phục gọn gàng ôm dáng.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Preview State Content (Hidden Initially) -->
                        <div id="uploadPreview" class="hidden w-full h-full flex flex-col items-center gap-4">
                            <div class="relative max-w-xs rounded-xl overflow-hidden border border-gray-100 shadow-md aspect-[3/4]">
                                <img id="previewImg" src="" alt="User photo preview" class="w-full h-full object-cover">
                                <button 
                                    type="button" 
                                    onclick="event.stopPropagation(); resetUpload();" 
                                    class="absolute top-2 right-2 w-8 h-8 rounded-full bg-black/60 hover:bg-black text-white flex items-center justify-center backdrop-blur-sm transition-all focus:outline-none"
                                    title="Chọn ảnh khác"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="text-xs text-gray-500 flex items-center gap-2">
                                <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                Đã tải ảnh lên thành công
                            </div>
                        </div>
                    </div>

                    @error('user_photo')
                        <p class="text-rose-500 text-xs mt-2 font-medium">{{ $message }}</p>
                    @enderror

                    <!-- Submit Action Button -->
                    <button 
                        type="submit" 
                        id="submitBtn" 
                        disabled 
                        class="w-full bg-gray-300 text-white py-4 px-6 rounded-2xl font-bold uppercase tracking-wider transition-all duration-300 mt-8 flex items-center justify-center gap-2 disabled:cursor-not-allowed cursor-pointer focus:outline-none"
                    >
                        <span>Thử Đồ Ngay với AI</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Fullscreen Premium Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-[#FDFBF7]/90 z-[9999] flex flex-col items-center justify-center px-4 hidden opacity-0 transition-opacity duration-300">
    <div class="max-w-md text-center flex flex-col items-center gap-6">
        <!-- Modern Spinner -->
        <div class="relative w-24 h-24">
            <div class="w-full h-full rounded-full border-[3px] border-gray-100"></div>
            <div class="absolute inset-0 rounded-full border-[3px] border-t-[#5c7a6b] border-r-[#5c7a6b] animate-spin"></div>
            <div class="absolute inset-4 rounded-full bg-[#5c7a6b]/5 flex items-center justify-center">
                <svg class="w-8 h-8 text-[#5c7a6b] animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 21l8.904-8.904m-8.904 0L21 3m-8.904 4L17 7"></path>
                </svg>
            </div>
        </div>

        <div class="flex flex-col gap-2">
            <h3 class="text-xl font-bold text-gray-900" id="loadingTitle">Đang chuẩn bị hình ảnh...</h3>
            <p class="text-sm text-gray-500" id="loadingSubtitle">Hệ thống đang tải lên và tối ưu kích thước ảnh mẫu.</p>
        </div>

        <!-- Progress Steps -->
        <div class="w-64 flex flex-col gap-3 mt-4 text-left border border-gray-100 bg-white rounded-2xl p-4 shadow-sm">
            <div class="flex items-center gap-3 text-xs" id="step1">
                <div class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px]">✓</div>
                <span class="font-medium text-gray-900">Tải ảnh chân dung của bạn</span>
            </div>
            <div class="flex items-center gap-3 text-xs" id="step2">
                <div class="w-5 h-5 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center text-[10px]" id="step2_num">2</div>
                <span class="text-gray-400" id="step2_text">Kết nối dịch vụ AI Hugging Face IDM-VTON</span>
            </div>
            <div class="flex items-center gap-3 text-xs" id="step3">
                <div class="w-5 h-5 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center text-[10px]" id="step3_num">3</div>
                <span class="text-gray-400" id="step3_text">Ghép thử trang phục và trả kết quả</span>
            </div>
        </div>
        <p class="text-[11px] text-[#5c7a6b] italic font-medium animate-bounce mt-4">
            Quá trình này mất khoảng 15-20 giây. Vui lòng không đóng trình duyệt.
        </p>
    </div>
</div>

<script>
    // Sync Category values
    const categorySelect = document.getElementById('category_select');
    const hiddenCategory = document.getElementById('hidden_category');
    
    categorySelect.addEventListener('change', function() {
        hiddenCategory.value = this.value;
    });

    // File Upload Preview & Validation
    const userPhotoInput = document.getElementById('userPhoto');
    const uploadArea = document.getElementById('uploadArea');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const uploadPreview = document.getElementById('uploadPreview');
    const previewImg = document.getElementById('previewImg');
    const submitBtn = document.getElementById('submitBtn');

    userPhotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        // Kiểm tra kích thước file (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File ảnh quá lớn! Vui lòng chọn ảnh dưới 5MB.');
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(ev) {
            previewImg.src = ev.target.result;
            
            // Switch view states
            uploadPlaceholder.classList.add('hidden');
            uploadPreview.classList.remove('hidden');
            
            // Enable action button
            submitBtn.disabled = false;
            submitBtn.style.backgroundColor = '#5c7a6b'; // active colors
            submitBtn.classList.remove('bg-gray-300');
            submitBtn.classList.add('hover:bg-[#4a6356]');
        };
        reader.readAsDataURL(file);
    });

    function resetUpload() {
        userPhotoInput.value = '';
        previewImg.src = '';
        uploadPreview.classList.add('hidden');
        uploadPlaceholder.classList.remove('hidden');
        
        submitBtn.disabled = true;
        submitBtn.style.backgroundColor = '';
        submitBtn.classList.add('bg-gray-300');
        submitBtn.classList.remove('hover:bg-[#4a6356]');
    }

    // Drag and Drop implementation
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, (e) => {
            e.preventDefault();
            uploadArea.classList.add('border-[#5c7a6b]', 'bg-[#5c7a6b]/5');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, (e) => {
            e.preventDefault();
            uploadArea.classList.remove('border-[#5c7a6b]', 'bg-[#5c7a6b]/5');
        }, false);
    });

    uploadArea.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length) {
            userPhotoInput.files = files;
            // Dispatch change event to trigger preview
            const event = new Event('change', { bubbles: true });
            userPhotoInput.dispatchEvent(event);
        }
    });

    // Form Submission & Animated Overlay
    const tryonForm = document.getElementById('tryonForm');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const loadingTitle = document.getElementById('loadingTitle');
    const loadingSubtitle = document.getElementById('loadingSubtitle');

    tryonForm.addEventListener('submit', function() {
        // Show loading screen
        loadingOverlay.classList.remove('hidden');
        // Fade in
        setTimeout(() => {
            loadingOverlay.classList.remove('opacity-0');
        }, 50);

        // Step animations (Simulate timeline changes to keep user updated)
        // Step 1 done immediately
        
        // At 3 seconds
        setTimeout(() => {
            const step2 = document.getElementById('step2');
            const step2_num = document.getElementById('step2_num');
            const step2_text = document.getElementById('step2_text');
            
            step2_num.textContent = '✓';
            step2_num.className = 'w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px]';
            step2_text.className = 'font-medium text-gray-900';
            
            loadingTitle.textContent = 'Dịch vụ AI đã tiếp nhận...';
            loadingSubtitle.textContent = 'Ảnh đang được gửi lên Hugging Face IDM-VTON cloud.';
        }, 3000);

        // At 7 seconds
        setTimeout(() => {
            const step3 = document.getElementById('step3');
            const step3_num = document.getElementById('step3_num');
            const step3_text = document.getElementById('step3_text');
            
            step3_num.textContent = '↻';
            step3_num.className = 'w-5 h-5 rounded-full bg-amber-500 text-white flex items-center justify-center text-[10px] animate-spin';
            step3_text.className = 'font-medium text-gray-900';
            
            loadingTitle.textContent = 'Trí tuệ nhân tạo đang xử lý...';
            loadingSubtitle.textContent = 'Sử dụng mô hình khuếch tán (Diffusion Model) để may và tối ưu nếp nhăn vải.';
        }, 7000);

        // At 14 seconds
        setTimeout(() => {
            loadingTitle.textContent = 'Đang nhận diện khớp nối...';
            loadingSubtitle.textContent = 'Đang căn chỉnh các khớp tay, vai và nếp gấp trang phục.';
        }, 14000);
    });
</script>
</x-app-layout>
