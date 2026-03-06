<x-admin-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumbs & Header -->
        <div class="flex items-center justify-between">
            <div class="flex flex-col gap-1">
                <nav class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-widest">
                    <a href="{{ route('admin.products.index') }}" class="hover:text-emerald-600 transition-colors">Sản phẩm</a>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    <span class="text-emerald-600">Chỉnh sửa</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900">Chỉnh sửa sản phẩm</h1>
            </div>
            <a href="{{ route('admin.products.index') }}" class="p-2 text-gray-400 hover:text-gray-900 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </a>
        </div>

        @if (session('error') || $errors->any())
            <div class="p-4 bg-rose-50 border border-rose-100 text-rose-700 rounded-xl text-sm animate-fade-in">
                <div class="flex items-center gap-3 mb-2 font-bold uppercase tracking-wider text-[11px]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                    Thông báo lỗi
                </div>
                <ul class="list-disc pl-5 space-y-1 font-medium">
                    @if(session('error')) <li>{{ session('error') }}</li> @endif
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="productForm">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left: Image Section -->
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                        <label class="text-[11px] font-bold text-gray-500 uppercase tracking-widest block">Ảnh sản phẩm</label>
                        
                        <!-- Multi-image Grid -->
                        <div id="imagePreviewContainer" class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            
                            @foreach($product->images->sortBy('sort_order') as $img)
                            {{-- Outer wrapper: relative, no overflow-hidden, so X button is not clipped --}}
                            <div class="relative aspect-[3/4] rounded-xl" data-img-id="{{ $img->id }}" style="border:1px solid #e5e7eb;">
                                {{-- Inner image container with overflow-hidden --}}
                                <div class="absolute inset-0 rounded-xl overflow-hidden">
                                    <img src="{{ $img->url }}" class="w-full h-full object-cover">
                                    @if($img->is_primary)
                                        <div style="position:absolute;bottom:8px;left:8px;right:8px;background:#059669;color:white;font-size:10px;font-weight:700;text-align:center;padding:4px 0;border-radius:6px;">Ảnh chính</div>
                                    @endif
                                </div>

                                {{-- Delete overlay (outside overflow-hidden div) --}}
                                <div class="delete-overlay" style="position:absolute;inset:0;background:rgba(239,68,68,0.65);display:none;align-items:center;justify-content:center;border-radius:0.75rem;z-index:5;">
                                    <div style="text-align:center;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 4px;display:block;"><polyline points="3 6 5 6 21 6"/><path d="m19 6-.867 12.142A2 2 0 0 1 16.138 20H7.862a2 2 0 0 1-1.995-1.858L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                        <span style="color:white;font-size:11px;font-weight:700;text-transform:uppercase;">Sẽ xóa</span>
                                    </div>
                                </div>

                                {{-- X button (outside overflow-hidden div, always visible) --}}
                                <label style="position:absolute;top:8px;right:8px;cursor:pointer;z-index:10;">
                                    <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" class="delete-img-checkbox" style="display:none;" onchange="toggleDeleteOverlay(this)">
                                    <div class="delete-toggle-btn" style="width:28px;height:28px;background:rgba(0,0,0,0.6);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 1px 4px rgba(0,0,0,0.35);" onmouseenter="this.style.background='#ef4444'" onmouseleave="if(!this.closest('label').querySelector('input').checked) this.style.background='rgba(0,0,0,0.6)'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                    </div>
                                </label>
                            </div>
                            @endforeach

                            <!-- Add More Images Button -->
                            <label class="relative aspect-[3/4] rounded-xl bg-gray-50 border-2 border-dashed border-gray-200 flex flex-col items-center justify-center cursor-pointer hover:border-emerald-300 transition-all group">
                                <div class="w-10 h-10 bg-white rounded-full shadow-sm flex items-center justify-center mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 group-hover:text-emerald-500"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                                </div>
                                <span class="text-[10px] font-bold text-gray-500 text-center px-2">Thêm ảnh mới</span>
                                <input type="file" name="image_files[]" id="imageFileInput" class="hidden" accept="image/*" multiple>
                            </label>
                            <!-- New image previews inserted here by JS -->
                        </div>

                    </div>
                </div>

                <!-- Right: Content Section -->
                <div class="md:col-span-2 space-y-6" x-data="{
                    selectedCategories: @js(old('categories', $selectedCategories)).map(id => id.toString()),
                    selectedSizes: @js(old('sizes', $selectedSizes)).map(id => id.toString()),
                    selectedColors: @js(old('colors', $selectedColors)).map(id => id.toString()),
                    sizeOptions: @js($sizes->map(fn($s) => ['id' => $s->id, 'name' => $s->value])),
                    colorOptions: @js($colors->map(fn($c) => ['id' => $c->id, 'name' => $c->value, 'hex' => $c->color_hex ?? '#000000'])),
                    categoryOptions: @js($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'parent' => $c->parent ? $c->parent->name : null])),
                    existingStock: @js($variantStock),
                    
                    get combinations() {
                        if (this.selectedSizes.length === 0 && this.selectedColors.length === 0) return [];
                        
                        let sizes = this.selectedSizes.length > 0 ? this.selectedSizes : [0];
                        let colors = this.selectedColors.length > 0 ? this.selectedColors : [0];
                        
                        let result = [];
                        colors.forEach(cId => {
                            sizes.forEach(sId => {
                                let sName = this.sizeOptions.find(o => o.id.toString() === sId.toString())?.name || (sId === 0 ? '--' : sId);
                                let cName = this.colorOptions.find(o => o.id.toString() === cId.toString())?.name || (cId === 0 ? '--' : cId);
                                let stock = this.existingStock[cId + '_' + sId] !== undefined ? this.existingStock[cId + '_' + sId] : 0;
                                result.push({ sId, cId, sName, cName, stock });
                            });
                        });
                        return result;
                    },
                    addNewSize(val) {
                        val = val.trim().toUpperCase();
                        if (val !== '') {
                            const existing = this.sizeOptions.find(o => o.name.toUpperCase() === val);
                            if (existing) {
                                if (!this.selectedSizes.includes(existing.id.toString())) this.selectedSizes.push(existing.id.toString());
                            } else {
                                if (!this.selectedSizes.includes(val)) this.selectedSizes.push(val);
                            }
                        }
                    },
                    addNewColor(val) {
                        val = val.trim();
                        if (val !== '') {
                            const existing = this.colorOptions.find(o => o.name.toLowerCase() === val.toLowerCase());
                            if (existing) {
                                if (!this.selectedColors.includes(existing.id.toString())) this.selectedColors.push(existing.id.toString());
                            } else {
                                if (!this.selectedColors.includes(val)) this.selectedColors.push(val);
                            }
                        }
                    }
                }">
                    <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm space-y-6">
                        <!-- Name & Category -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="name" class="text-[11px] font-bold text-gray-500 uppercase tracking-widest block">Tên sản phẩm *</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                                       class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all placeholder:text-gray-400">
                                @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="base_price" class="text-[11px] font-bold text-gray-500 uppercase tracking-widest block">Giá bán lẻ *</label>
                                <input type="number" name="base_price" id="base_price" value="{{ old('base_price', (int)$product->base_price) }}" required
                                       class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                                @error('base_price') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Variant Stock Table -->
                        <div x-show="combinations.length > 0" x-transition class="space-y-4 pt-4 border-t border-gray-50">
                            <div class="flex items-center justify-between">
                                <label class="text-[11px] font-bold text-gray-500 uppercase tracking-widest block">Quản lý tồn kho theo biến thể</label>
                                <span class="text-[10px] font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full" x-text="combinations.length + ' biến thể được tạo'"></span>
                            </div>
                            
                            <div class="overflow-x-auto rounded-2xl border border-gray-100">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50 text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                                        <tr>
                                            <th class="px-4 py-3">Màu sắc</th>
                                            <th class="px-4 py-3">Kích thước</th>
                                            <th class="px-4 py-3 w-32">Số lượng tồn</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        <template x-for="combo in combinations" :key="combo.cId + '-' + combo.sId">
                                            <tr class="hover:bg-gray-50/50 transition-colors">
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-2">
                                                        <div x-show="combo.cId !== 0 && colorOptions.find(o => o.id.toString() === combo.cId.toString())?.hex" 
                                                             class="w-3 h-3 rounded-full border border-gray-200"
                                                             :style="'background-color: ' + colorOptions.find(o => o.id.toString() === combo.cId.toString())?.hex"></div>
                                                        <span class="font-medium text-gray-700" x-text="combo.cName"></span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="font-medium text-gray-700" x-text="combo.sName"></span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="number" :name="'variant_stock[' + combo.cId + '][' + combo.sId + ']'" 
                                                           :value="combo.stock"
                                                           required
                                                           class="w-full bg-white border-gray-100 rounded-lg px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-bold text-gray-900 shadow-sm">
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Category Selection -->
                            <div class="space-y-2">
                                <label class="text-[11px] font-bold text-gray-500 uppercase tracking-widest block">Danh mục *</label>
                                
                                <div class="relative" x-data="{ open: false, search: '' }" @click.away="open = false">
                                    <button type="button" @click="open = !open" 
                                            class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm text-left flex items-center justify-between hover:border-emerald-500 transition-all focus:ring-2 focus:ring-emerald-500/10">
                                        <span x-text="selectedCategories.length ? categoryOptions.filter(o => selectedCategories.includes(o.id.toString())).map(o => o.name).join(', ') : 'Chọn danh mục sản phẩm...'" 
                                              class="truncate pr-4" :class="selectedCategories.length ? 'text-gray-900 font-medium' : 'text-gray-400'"></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"><path d="m6 9 6 6 6-6"/></svg>
                                    </button>

                                    <div x-show="open" x-transition 
                                         class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden animate-fade-in">
                                        <div class="p-3 border-b border-gray-50 bg-gray-50/50">
                                            <input type="text" x-model="search" placeholder="Tìm kiếm danh mục..." 
                                                   class="w-full bg-white border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                                        </div>
                                        <div class="max-h-60 overflow-y-auto p-2 space-y-1">
                                            <template x-for="option in categoryOptions.filter(o => !search || o.name.toLowerCase().includes(search.toLowerCase()))" :key="option.id">
                                                <label class="flex flex-col p-3 rounded-xl cursor-pointer transition-all group"
                                                       :class="selectedCategories.includes(option.id.toString()) ? 'bg-emerald-600 text-white shadow-md' : 'hover:bg-gray-50 text-gray-600'">
                                                    <input type="checkbox" :value="option.id.toString()" x-model="selectedCategories" class="sr-only">
                                                    <div class="flex flex-col">
                                                        <span class="text-xs font-bold uppercase tracking-wide transition-colors" x-text="option.name" :class="selectedCategories.includes(option.id.toString()) ? 'text-white' : 'group-hover:text-emerald-600'"></span>
                                                        <template x-if="option.parent">
                                                            <span class="text-[10px] italic transition-colors" x-text="'/ ' + option.parent" :class="selectedCategories.includes(option.id.toString()) ? 'text-emerald-100' : 'opacity-60 text-gray-400'"></span>
                                                        </template>
                                                    </div>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <!-- Hidden Actual Inputs for Form Submission -->
                                <template x-for="id in selectedCategories" :key="id">
                                    <input type="hidden" name="categories[]" :value="id">
                                </template>
                                @error('categories') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Size Selection -->
                            <div class="space-y-2">
                                <label class="text-[11px] font-bold text-gray-500 uppercase tracking-widest block">Size sản phẩm</label>
                                
                                <div class="relative" x-data="{ open: false, search: '' }" @click.away="open = false">
                                    <button type="button" @click="open = !open" 
                                            class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm text-left flex items-center justify-between hover:border-emerald-500 transition-all focus:ring-2 focus:ring-emerald-500/10">
                                        <span x-text="selectedSizes.length ? selectedSizes.map(id => { let o = sizeOptions.find(opt => opt.id.toString() === id.toString()); return o ? o.name : id; }).join(', ') : 'Chọn hoặc thêm size (S, M, L...)'" 
                                              class="truncate pr-4" :class="selectedSizes.length ? 'text-gray-900 font-medium' : 'text-gray-400'"></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"><path d="m6 9 6 6 6-6"/></svg>
                                    </button>

                                    <div x-show="open" x-transition 
                                         class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden animate-fade-in">
                                        <div class="p-3 border-b border-gray-50 bg-gray-50/50">
                                            <div class="flex gap-2">
                                                <input type="text" x-model="search" @keydown.enter.prevent="addNewSize(search); search=''" placeholder="Tìm hoặc nhập size mới..." 
                                                       class="w-full bg-white border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                                                <button type="button" @click="addNewSize(search); search=''" x-show="search.trim() !== ''" class="px-3 py-2 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition-all">Thêm</button>
                                            </div>
                                        </div>
                                        <div class="max-h-60 overflow-y-auto p-2 space-y-1">
                                            <template x-for="customSize in selectedSizes.filter(s => !sizeOptions.find(o => o.id.toString() === s.toString()))" :key="customSize">
                                                <label class="flex flex-col p-3 rounded-xl cursor-pointer transition-all group bg-emerald-600 text-white shadow-md">
                                                    <input type="checkbox" :value="customSize" x-model="selectedSizes" class="sr-only">
                                                    <div class="flex flex-col">
                                                        <span class="text-xs font-bold uppercase tracking-wide transition-colors text-white" x-text="customSize"></span>
                                                        <span class="text-[10px] italic text-emerald-100 mt-0.5">Size mới</span>
                                                    </div>
                                                </label>
                                            </template>

                                            <template x-for="option in sizeOptions.filter(o => !search || o.name.toLowerCase().includes(search.toLowerCase()))" :key="option.id">
                                                <label class="flex flex-col p-3 rounded-xl cursor-pointer transition-all group"
                                                       :class="selectedSizes.includes(option.id.toString()) ? 'bg-emerald-600 text-white shadow-md' : 'hover:bg-gray-50 text-gray-600'">
                                                    <input type="checkbox" :value="option.id.toString()" x-model="selectedSizes" class="sr-only">
                                                    <div class="flex flex-col">
                                                        <span class="text-xs font-bold uppercase tracking-wide transition-colors" x-text="option.name" :class="selectedSizes.includes(option.id.toString()) ? 'text-white' : 'group-hover:text-emerald-600'"></span>
                                                    </div>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <template x-for="val in selectedSizes" :key="val">
                                    <input type="hidden" name="sizes[]" :value="val">
                                </template>
                            </div>

                            <!-- Color Selection -->
                            <div class="space-y-2">
                                <label class="text-[11px] font-bold text-gray-500 uppercase tracking-widest block">Màu sắc</label>
                                
                                <div class="relative" x-data="{ open: false, search: '' }" @click.away="open = false">
                                    <button type="button" @click="open = !open" 
                                            class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm text-left flex items-center justify-between hover:border-emerald-500 transition-all focus:ring-2 focus:ring-emerald-500/10">
                                        <span x-text="selectedColors.length ? selectedColors.map(id => { let o = colorOptions.find(opt => opt.id.toString() === id.toString()); return o ? o.name : id; }).join(', ') : 'Chọn hoặc thêm màu sắc...'" 
                                              class="truncate pr-4" :class="selectedColors.length ? 'text-gray-900 font-medium' : 'text-gray-400'"></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"><path d="m6 9 6 6 6-6"/></svg>
                                    </button>

                                    <div x-show="open" x-transition 
                                         class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden animate-fade-in">
                                        <div class="p-3 border-b border-gray-50 bg-gray-50/50">
                                            <div class="flex gap-2">
                                                <input type="text" x-model="search" @keydown.enter.prevent="addNewColor(search); search=''" placeholder="Tìm hoặc nhập màu mới..." 
                                                       class="w-full bg-white border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                                                <button type="button" @click="addNewColor(search); search=''" x-show="search.trim() !== ''" class="px-3 py-2 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition-all">Thêm</button>
                                            </div>
                                        </div>
                                        <div class="max-h-60 overflow-y-auto p-2 space-y-1">
                                            <template x-for="customColor in selectedColors.filter(s => !colorOptions.find(o => o.id.toString() === s.toString()))" :key="customColor">
                                                <label class="flex flex-col p-3 rounded-xl cursor-pointer transition-all group bg-emerald-600 text-white shadow-md">
                                                    <input type="checkbox" :value="customColor" x-model="selectedColors" class="sr-only">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs font-bold uppercase tracking-wide transition-colors text-white" x-text="customColor"></span>
                                                        <span class="text-[10px] italic text-emerald-100">Mới</span>
                                                    </div>
                                                </label>
                                            </template>

                                            <template x-for="option in colorOptions.filter(o => !search || o.name.toLowerCase().includes(search.toLowerCase()))" :key="option.id">
                                                <label class="flex flex-col p-3 rounded-xl cursor-pointer transition-all group"
                                                       :class="selectedColors.includes(option.id.toString()) ? 'bg-emerald-600 text-white shadow-md' : 'hover:bg-gray-50 text-gray-600'">
                                                    <input type="checkbox" :value="option.id.toString()" x-model="selectedColors" class="sr-only">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-xs font-bold uppercase tracking-wide transition-colors" x-text="option.name" :class="selectedColors.includes(option.id.toString()) ? 'text-white' : 'group-hover:text-emerald-600'"></span>
                                                        <template x-if="option.hex">
                                                            <div class="w-4 h-4 rounded-full border border-gray-200 shadow-sm" :style="'background-color: ' + option.hex"></div>
                                                        </template>
                                                    </div>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <template x-for="val in selectedColors" :key="val">
                                    <input type="hidden" name="colors[]" :value="val">
                                </template>
                                @error('colors') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="description" class="text-[11px] font-bold text-gray-500 uppercase tracking-widest block">Mô tả tóm tắt</label>
                            <textarea name="description" id="description" rows="4" 
                                      class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">{{ old('description', $product->description) }}</textarea>
                            @error('description') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Submit -->
                        <div class="pt-4 flex items-center gap-3">
                            <button type="submit" class="px-8 py-3 bg-emerald-600 text-white rounded-xl text-sm font-bold hover:bg-emerald-700 transition-all shadow-sm shadow-emerald-200 flex items-center justify-center gap-2 flex-1 md:flex-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                Cập nhật sản phẩm
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="px-8 py-3 bg-white text-gray-500 rounded-xl text-sm font-bold hover:bg-gray-50 transition-all border border-gray-200">
                                Hủy bỏ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('imageFileInput');
            const container = document.getElementById('imagePreviewContainer');
            const addBtn = fileInput.parentElement; // the label
            
            // DataTransfer object to hold all files to be uploaded
            let dt = new DataTransfer();

            function updateFileInput() {
                fileInput.files = dt.files;
            }

            function addFileToPreview(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    // Add a unique ID to identify which file this matches in the DataTransfer
                    const fileId = 'new_' + Math.random().toString(36).substr(2, 9);
                    div.id = fileId;
                    div.className = 'relative aspect-[3/4] rounded-xl overflow-hidden new-preview border-2 border-emerald-400 shadow-sm transition-all hover:scale-[1.02]';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                        <div class="absolute bottom-1 left-1 right-1 bg-emerald-600/90 backdrop-blur-sm text-white text-[9px] font-bold text-center py-1 rounded-lg">Mới thêm</div>
                        <button type="button" class="remove-new-img absolute top-1 right-1 w-6 h-6 bg-rose-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-rose-600 transition-colors z-10">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    `;
                    
                    // Handle removal
                    div.querySelector('.remove-new-img').addEventListener('click', function() {
                        // Find index by file context (name + size is usually enough for local uniqueness)
                        const newDt = new DataTransfer();
                        let found = false;
                        for(let i = 0; i < dt.items.length; i++) {
                            const f = dt.items[i].getAsFile();
                            // If we haven't found the match yet and this file looks like the one we're removing
                            if(!found && f.name === file.name && f.size === file.size && f.lastModified === file.lastModified) {
                                found = true;
                                continue;
                            }
                            newDt.items.add(f);
                        }
                        dt = newDt;
                        updateFileInput();
                        div.remove();
                    });

                    container.insertBefore(div, addBtn);
                }
                reader.readAsDataURL(file);
            }

            fileInput.addEventListener('change', function(e) {
                if (this.files) {
                    Array.from(this.files).forEach((file) => {
                        dt.items.add(file);
                        addFileToPreview(file);
                    });
                    updateFileInput();
                }
            });

            // Handle Paste
            window.addEventListener('paste', e => {
                const items = e.clipboardData.items;
                for (let i = 0; i < items.length; i++) {
                    if (items[i].type.indexOf('image') !== -1) {
                        const blob = items[i].getAsFile();
                        const timestamp = new Date().getTime();
                        const file = new File([blob], `pasted_${timestamp}.png`, { type: blob.type });
                        
                        dt.items.add(file);
                        addFileToPreview(file);
                        updateFileInput();
                    }
                }
            });
        });

        function toggleDeleteOverlay(checkbox) {
            const card = checkbox.closest('[data-img-id]');
            const overlay = card.querySelector('.delete-overlay');
            const btn = card.querySelector('.delete-toggle-btn');
            if (checkbox.checked) {
                overlay.style.display = 'flex';
                btn.style.background = '#ef4444';
            } else {
                overlay.style.display = 'none';
                btn.style.background = 'rgba(0,0,0,0.65)';
            }
        }
    </script>
</x-admin-layout>
