<x-app-layout>
<div class="font-[Inter] text-[#333333]">
    <!-- Breadcrumb -->
    <div class="max-w-[1440px] px-4 md:px-8 mx-auto mt-8 mb-6">
        <nav class="text-base text-gray-500" aria-label="Breadcrumb">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="/" class="hover:text-gray-900 transition-colors">Trang Chủ</a>
                    <span class="mx-2">/</span>
                </li>
                <li class="flex items-center">
                    <a href="{{ route('collection') }}" class="hover:text-gray-900 transition-colors">Tất Cả Sản Phẩm</a>
                    <span class="mx-2">/</span>
                </li>
                <li class="flex items-center">
                    <span class="text-gray-900">{{ $product['name'] }}</span>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Main Product Section -->
    <div class="max-w-[1440px] px-4 md:px-8 mx-auto flex flex-col md:flex-row gap-8 lg:gap-16 mb-20 relative">
        
        <!-- Left: Image Gallery (Sticky on Desktop) -->
        <div class="w-full md:w-[60%] lg:w-[65%] flex flex-col-reverse md:flex-row gap-4 h-max md:sticky md:top-24">
            
            <!-- Thumbnails (Vertical on Desktop, Horizontal on Mobile) -->
            <div class="flex md:flex-col gap-3 overflow-x-auto md:overflow-visible w-full md:w-20 lg:w-24 shrink-0 px-1 py-1 hide-scrollbar">
                @foreach($product['images'] as $index => $image)
                    <button class="w-20 md:w-full aspect-[3/4] border-2 {{ $index === 0 ? 'border-[#5c7a6b]' : 'border-transparent' }} overflow-hidden shrink-0 transition-all hover:border-gray-300 focus:outline-none" onclick="changeMainImage(this, '{{ $image }}')">
                        <img src="{{ $image }}" class="w-full h-full object-cover cursor-pointer" alt="{{ $product['name'] }} thumbnail {{ $index + 1 }}">
                    </button>
                @endforeach
            </div>

            <!-- Main Image -->
            <div class="flex-1 w-full bg-gray-50 aspect-[3/4] relative overflow-hidden">
                <img id="main-product-image" src="{{ $product['images'][0] }}" class="w-full h-full object-cover transition-opacity duration-300" alt="{{ $product['name'] }}">
            </div>
        </div>

        <!-- Right: Product Details -->
        <div class="w-full md:w-[40%] lg:w-[35%] flex flex-col mt-4 md:mt-0">
            
            <!-- Title & ID -->
            <h1 class="text-3xl font-bold mb-2">{{ $product['name'] }}</h1>
            <p class="text-sm text-gray-500 mb-4">Mã SP: {{ $product['id'] }}</p>
            
            <!-- Price -->
            <div class="inline-block border border-gray-300 px-4 py-2 text-2xl font-bold mb-6">{{ $product['price'] }}</div>

            <!-- Description & Size Guide Container -->
            <div id="product-info-container" class="mb-8">
                <!-- Description -->
                <p id="product-description" class="text-gray-600 leading-relaxed transition-opacity duration-300">
                    {{ $product['description'] }}
                </p>

                <!-- Size Guide Inline (Hidden by default) -->
                <div id="inline-size-guide" class="hidden transition-opacity duration-300 border-t border-gray-300 pt-6 mt-2">
                    <div class="flex justify-between items-end mb-1">
                        <h3 class="text-xl font-bold text-gray-900 uppercase tracking-wide">Bảng Hướng Dẫn Chọn Size</h3>
                        <button class="text-sm text-gray-500 hover:text-black underline underline-offset-4" onclick="toggleSizeGuide()">Đóng</button>
                    </div>
                    <p class="text-base text-gray-500 mb-6">Số đo cơ thể (cm), không phải số đo trang phục.</p>
                    <div class="overflow-x-auto border border-gray-200">
                        <table class="w-full text-center text-base">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200 text-gray-900">
                                    <th class="px-4 py-4 font-semibold">Size</th>
                                    <th class="px-4 py-4 font-semibold">Ngực</th>
                                    <th class="px-4 py-4 font-semibold">Eo</th>
                                    <th class="px-4 py-4 font-semibold">Mông</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="px-4 py-4 font-medium text-gray-900">XS</td><td class="px-4 py-4 text-gray-600">78-82</td><td class="px-4 py-4 text-gray-600">60-64</td><td class="px-4 py-4 text-gray-600">86-90</td>
                                </tr>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="px-4 py-4 font-medium text-gray-900">S</td><td class="px-4 py-4 text-gray-600">82-86</td><td class="px-4 py-4 text-gray-600">64-68</td><td class="px-4 py-4 text-gray-600">90-94</td>
                                </tr>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="px-4 py-4 font-medium text-gray-900">M</td><td class="px-4 py-4 text-gray-600">86-90</td><td class="px-4 py-4 text-gray-600">68-72</td><td class="px-4 py-4 text-gray-600">94-98</td>
                                </tr>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="px-4 py-4 font-medium text-gray-900">L</td><td class="px-4 py-4 text-gray-600">90-94</td><td class="px-4 py-4 text-gray-600">72-76</td><td class="px-4 py-4 text-gray-600">98-102</td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 font-medium text-gray-900">XL</td><td class="px-4 py-4 text-gray-600">94-98</td><td class="px-4 py-4 text-gray-600">76-80</td><td class="px-4 py-4 text-gray-600">102-106</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mb-6 border-t border-gray-200 pt-6">
                <h3 class="font-medium text-sm text-gray-900 uppercase tracking-wide mb-3">Màu Sắc: <span id="selected-color-name" class="font-normal">{{ $product['colors'][0]['name'] }}</span></h3>
                <div class="flex items-center gap-3">
                    @foreach($product['colors'] as $index => $color)
                        <button class="w-8 h-8 rounded-full border-2 {{ $index === 0 ? 'border-gray-900' : 'border-gray-200' }} p-[2px] focus:outline-none transition-all hover:border-gray-400" onclick="selectColor(this, '{{ $color['name'] }}')">
                            <span class="block w-full h-full rounded-full border border-gray-100" style="background-color: {{ $color['hex'] }}"></span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Size Selection -->
            <div class="mb-8 border-t border-gray-200 pt-6">
                <div class="flex justify-between items-end mb-3">
                    <h3 class="font-medium text-sm text-gray-900 uppercase tracking-wide">Kích Cỡ: <span id="selected-size" class="font-normal">Select Size</span></h3>
                    <button class="text-sm text-[#5c7a6b] underline underline-offset-4 hover:text-[#4a6356] transition-colors" onclick="toggleSizeGuide()">Hướng Dẫn Chọn Size</button>
                </div>
                
                <div class="grid grid-cols-5 gap-2">
                    @foreach($product['sizes'] as $size)
                        <button class="size-btn border border-gray-300 py-3 text-sm font-medium hover:border-gray-900 focus:outline-none transition-all {{ in_array($size, ['3XL', '4XL', '5XL', '6XL']) ? 'bg-gray-50 text-gray-400 cursor-not-allowed' : '' }}" {{ in_array($size, ['3XL', '4XL', '5XL', '6XL']) ? 'disabled' : '' }} onclick="selectSize(this, '{{ $size }}')">
                            {{ $size }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-3 mb-8">
                <button class="w-full bg-[#333] text-white py-4 font-semibold uppercase tracking-wider hover:bg-black transition-colors">
                    Thêm Vào Giỏ
                </button>
                <button class="w-full border border-gray-300 bg-white text-[#333] py-4 font-semibold hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                    <svg width="20" height="18" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16.5 5.5C16.5 8.5 9 14.5 9 14.5C9 14.5 1.5 8.5 1.5 5.5C1.5 3.5 3 2 5 2C6.5 2 7.7 2.8 8.5 4C9.3 2.8 10.5 2 12 2C14 2 16.5 3.5 16.5 5.5Z" stroke="#333333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Thêm Vào Yêu Thích
                </button>
            </div>
            
            <!-- Availability indicator -->
            <div class="flex gap-2 items-center text-sm font-medium mb-10 text-[#5c7a6b]">
                <div class="w-2 h-2 rounded-full bg-[#5c7a6b]"></div> Cỡ của bạn còn lại {{ $product['availability'] }} chiếc
            </div>

            <!-- Accordions -->
            <div class="border-t border-gray-200">
                
                <!-- Phom Dáng -->
                <div class="border-b border-gray-200">
                    <button class="w-full flex justify-between items-center py-5 font-medium text-left product-accordion-btn" data-target="fit-content">
                        PHOM DÁNG & TỈ LỆ
                        <span class="text-xl leading-none font-light toggle-icon">+</span>
                    </button>
                    <div id="fit-content" class="hidden pb-5 text-gray-600 text-sm leading-relaxed">
                        Phom dáng thể thao thoải mái. Kết hợp hoàn hảo với quần legging và quần dài.<br><br>
                        Người mẫu của chúng tôi mặc size S.
                    </div>
                </div>

                <!-- Chất Liệu -->
                <div class="border-b border-gray-200">
                    <button class="w-full flex justify-between items-center py-5 font-medium text-left product-accordion-btn" data-target="fabric-content">
                        CHẤT LIỆU
                        <span class="text-xl leading-none font-light toggle-icon">+</span>
                    </button>
                    <div id="fabric-content" class="hidden pb-5 text-gray-600 text-sm leading-relaxed">
                        Chất liệu chính: {{ $product['materials'] ?: 'Đang cập nhật' }}. Thân thiện và mang lại cảm giác thoải mái.
                    </div>
                </div>

                <!-- Chi Tiết Sản Phẩm -->
                <div class="border-b border-gray-200">
                    <button class="w-full flex justify-between items-center py-5 font-medium text-left product-accordion-btn" data-target="details-content">
                        CHI TIẾT MÔ TẢ SẢN PHẨM
                        <span class="text-xl leading-none font-light toggle-icon">+</span>
                    </button>
                    <div id="details-content" class="hidden pb-5 text-gray-600 text-sm space-y-2">
                        <ul class="list-disc pl-5">
                            <li>Cổ chữ V tinh tế</li>
                            <li>Thiết kế tay ngắn mát mẻ</li>
                            <li>Gấu áo bo thun nhẹ</li>
                            <li>Được tạo ra với sự bền vững cao nhất</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Related Products -->
 <div class="max-w-[1440px] px-8 md:px-16 lg:px-28 mx-auto mb-20">
    <h2 class="text-2xl font-bold mb-8 text-left uppercase tracking-wide">Có Thể Bạn Cũng Thích</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-10 gap-y-12">
        
        @foreach($relatedProducts as $related)
        <div class="product-card group cursor-pointer" onclick="window.location.href='{{ route('product.show', $related['slug']) }}'">
            <div class="relative w-full aspect-[3/4] overflow-hidden bg-[#f3f3f3] mb-4 flex items-center justify-center">
                <img src="{{ $related['image'] }}" alt="{{ $related['name'] }}" 
                     class="max-w-[95%] max-h-[95%] object-contain transition-transform duration-500 group-hover:scale-105">
                
                <button class="absolute top-4 right-4 text-gray-400 group-hover:text-black hover:!text-red-500 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                    </svg>
                </button>

                <!-- Quick Add Action -->
                <button class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-white text-black font-medium px-6 py-3 w-[85%] opacity-0 transform translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 hover:bg-black hover:text-white shadow-lg text-sm uppercase tracking-wide">
                    Thêm Nhanh
                </button>
            </div>
            
            <div class="flex flex-col px-4">
                <h3 class="font-bold text-sm md:text-base uppercase leading-tight group-hover:underline underline-offset-4">{{ $related['name'] }}</h3>
                <div class="flex justify-between items-start mt-0.5"> 
                    <p class="text-gray-500 text-xs md:text-sm leading-tight line-clamp-1"></p>
                    <span class="font-bold text-sm md:text-base ml-2 whitespace-nowrap">{{ $related['price'] }}</span>
                </div>
                <div class="flex gap-2 mt-3 text-transparent text-xs hover:text-gray-400">
                    <div class="w-4 h-4 rounded-full bg-gray-300 border border-gray-200 shadow-sm"></div>
                </div>
            </div>
        </div>
        @endforeach

    </div>
</div>

<!-- Removed Size Guide Modal -->

<style>
/* Hide scrollbar for Chrome, Safari and Opera */
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}
/* Hide scrollbar for IE, Edge and Firefox */
.hide-scrollbar {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
</style>

<script>
    // Handle Main Image change on Thumbnail Click
    function changeMainImage(btn, src) {
        // Update main image source
        const mainImage = document.getElementById('main-product-image');
        
        // Simple fade effect
        mainImage.style.opacity = '0';
        setTimeout(() => {
            mainImage.src = src;
            mainImage.style.opacity = '1';
        }, 150);

        // Update active thumbnail border
        const allThumbs = btn.parentElement.querySelectorAll('button');
        allThumbs.forEach(t => {
            t.classList.remove('border-[#5c7a6b]');
            t.classList.add('border-transparent');
        });
        
        btn.classList.add('border-[#5c7a6b]');
        btn.classList.remove('border-transparent');
    }

    // Handle Color Selection
    function selectColor(btn, name) {
        document.getElementById('selected-color-name').textContent = name;
        
        const allColorBtns = btn.parentElement.querySelectorAll('button');
        allColorBtns.forEach(c => {
            c.classList.remove('border-gray-900');
            c.classList.add('border-gray-200');
        });
        
        btn.classList.add('border-gray-900');
        btn.classList.remove('border-gray-200');
    }

    // Handle Size Selection
    function selectSize(btn, size) {
        if(btn.disabled) return;

        document.getElementById('selected-size').textContent = size;
        
        const allSizeBtns = document.querySelectorAll('.size-btn');
        allSizeBtns.forEach(s => {
            if(!s.disabled) {
                s.classList.remove('border-gray-900', 'bg-gray-900', 'text-white');
                s.classList.add('bg-white', 'text-[#333]');
            }
        });
        
        btn.classList.add('border-gray-900', 'bg-gray-900', 'text-white');
        btn.classList.remove('bg-white', 'text-[#333]');
    }

    // Handle Product Accordions
    document.addEventListener('DOMContentLoaded', function() {
        const accordions = document.querySelectorAll('.product-accordion-btn');
        
        accordions.forEach(acc => {
            acc.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const content = document.getElementById(targetId);
                const icon = this.querySelector('.toggle-icon');
                
                if (content.classList.contains('hidden')) {
                    // Automatically close others
                    accordions.forEach(other => {
                        if (other !== this) {
                            document.getElementById(other.getAttribute('data-target')).classList.add('hidden');
                            other.querySelector('.toggle-icon').textContent = '+';
                        }
                    });
                    
                    content.classList.remove('hidden');
                    icon.textContent = '-';
                } else {
                    content.classList.add('hidden');
                    icon.textContent = '+';
                }
            });
        });
    });

    // Toggle Inline Size Guide
    function toggleSizeGuide() {
        const description = document.getElementById('product-description');
        const sizeGuide = document.getElementById('inline-size-guide');
        
        if (sizeGuide.classList.contains('hidden')) {
            // Show Size Guide, Hide Description
            description.classList.add('hidden');
            sizeGuide.classList.remove('hidden');
        } else {
            // Show Description, Hide Size Guide
            sizeGuide.classList.add('hidden');
            description.classList.remove('hidden');
        }
    }
</script>
</x-app-layout>
