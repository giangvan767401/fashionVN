<x-app-layout>
    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-8 flex items-center space-x-4">
                <a href="{{ route('admin.categories.index') }}" class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-gray-400 hover:text-indigo-600 transition-all border border-gray-100 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Thêm danh mục mới</h1>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <form action="{{ route('admin.categories.store') }}" method="POST" class="p-8">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Name & Slug -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tên danh mục</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm text-gray-900 focus:ring-indigo-500 focus:border-indigo-500 p-3 transition-all"
                                    placeholder="Ví dụ: Quần Jean Nam">
                                @error('name') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="slug" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Đường dẫn (Slug)</label>
                                <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                                    class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm text-gray-900 focus:ring-indigo-500 focus:border-indigo-500 p-3 transition-all"
                                    placeholder="Tự động tạo nếu để trống">
                                @error('slug') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Parent Category & Sort Order -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="parent_id" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Danh mục cha</label>
                                <select name="parent_id" id="parent_id"
                                    class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm text-gray-900 focus:ring-indigo-500 focus:border-indigo-500 p-3 transition-all">
                                    <option value="">Không có (Danh mục gốc)</option>
                                    @foreach($parentCategories as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="sort_order" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Thứ tự hiển thị</label>
                                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                                    class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm text-gray-900 focus:ring-indigo-500 focus:border-indigo-500 p-3 transition-all">
                                @error('sort_order') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Mô tả</label>
                            <textarea name="description" id="description" rows="3"
                                class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm text-gray-900 focus:ring-indigo-500 focus:border-indigo-500 p-3 transition-all"
                                placeholder="Nhập mô tả cho danh mục...">{{ old('description') }}</textarea>
                            @error('description') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Image URL -->
                        <div>
                            <label for="image_url" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Đường dẫn hình ảnh (URL)</label>
                            <input type="text" name="image_url" id="image_url" value="{{ old('image_url') }}"
                                class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm text-gray-900 focus:ring-indigo-500 focus:border-indigo-500 p-3 transition-all"
                                placeholder="https://example.com/image.jpg">
                            @error('image_url') <p class="mt-1 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Visibility Toggle -->
                        <div class="flex items-center p-4 bg-gray-50 rounded-2xl border border-gray-100">
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-gray-900">Hiển thị danh mục</h4>
                                <p class="text-xs text-gray-500">Danh mục sẽ được hiển thị trên trang chủ và menu điều hướng.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', 1) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                    </div>

                    <div class="mt-10 pt-8 border-t border-gray-50 flex items-center justify-end space-x-4">
                        <a href="{{ route('admin.categories.index') }}" class="px-6 py-2 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">Hủy</a>
                        <button type="submit" class="px-8 py-2 bg-indigo-600 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 transition-all shadow-md" style="background-color: #4f46e5;">
                            Lưu danh mục
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
