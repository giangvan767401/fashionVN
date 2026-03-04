<x-app-layout>
    @php
        $defaultTab = 'dashboard';
        if ($errors->updatePassword->isNotEmpty()) {
            $defaultTab = 'edit-password';
        } elseif ($errors->userDeletion->isNotEmpty()) {
            $defaultTab = 'delete-account';
        } elseif ($errors->isNotEmpty() || session('status') === 'profile-updated') {
            $defaultTab = 'edit-info';
        }
    @endphp

    <div x-data="{ currentTab: '{{ $defaultTab }}' }" class="font-[Montserrat] bg-[#f7f8f9] min-h-screen py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; align-items: start;">
                
                <!-- Cột trái - Thông tin tài khoản -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center gap-4 mb-6 border-b border-gray-100 pb-6">
                        <!-- Avatar Chữ -->
                        <div class="w-16 h-16 rounded flex items-center justify-center bg-gray-50 text-gray-800 text-3xl font-semibold uppercase">
                            {{ substr(Auth::user()->full_name ?? Auth::user()->name ?? 'N', 0, 1) }}
                        </div>
                        <div>
                            <h2 class="text-xl text-gray-900 font-semibold tracking-wide">{{ mb_convert_case(Auth::user()->full_name ?? Auth::user()->name, MB_CASE_TITLE, "UTF-8") ?? 'Nhi Trinh Thi Lan' }}</h2>
                            <p class="text-[13px] text-gray-500 font-light mt-1">{{ Auth::user()->email ?? 'lannhitrinhthi0505@gmail.com' }}</p>
                        </div>
                    </div>

                    <div class="space-y-2 mb-8 text-[13px] text-gray-600 font-light">
                        <p><span class="font-medium text-gray-900">ID:</span> {{ Auth::user()->id ?? '1' }}</p>
                        <p><span class="font-medium text-gray-900">Username:</span> {{ Auth::user()->email ?? '' }}</p>
                        <p><span class="font-medium text-gray-900">Admin:</span> {{ Auth::user()->role_id == 1 ? 'Có' : 'Không' }}</p>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <a href="{{ route('profile.info') }}" 
                           style="display: block; width: 100%; padding: 10px 16px; text-align: center; font-size: 13px; font-weight: 500; border-radius: 6px; border: 1.5px solid #d1d5db; color: #374151; text-decoration: none; box-sizing: border-box;">
                            Chỉnh sửa thông tin
                        </a>
                        <a href="{{ route('profile.password') }}" 
                           style="display: block; width: 100%; padding: 10px 16px; text-align: center; font-size: 13px; font-weight: 500; border-radius: 6px; border: 1.5px solid #4a84f5; color: #4a84f5; text-decoration: none; box-sizing: border-box;">
                            Đổi mật khẩu
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" style="display: block; width: 100%; padding: 10px 16px; text-align: center; font-size: 13px; font-weight: 500; border-radius: 6px; border: none; color: white; cursor: pointer; background-color: #e53e3e;">
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Cột phải - Nội dung động (Tabs) -->
                <div style="display: flex; flex-direction: column; gap: 2rem;">
                    
                    <!-- Tab 1: Dashboard -->
                    <div x-show="currentTab === 'dashboard'" x-transition class="space-y-8">
                        <!-- Đơn hàng gần đây -->
                        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100/50">
                            <h3 class="text-base font-semibold text-gray-900 mb-6 tracking-wide">Đơn hàng gần đây</h3>
                            
                            <div class="space-y-6">
                                <!-- Order Item 1 -->
                                <div class="flex items-center justify-between border-b border-gray-50 pb-6 last:border-0 last:pb-0">
                                    <div>
                                        <p class="text-[14px] font-medium text-gray-900 mb-1">Đơn hàng #2</p>
                                        <p class="text-[12px] text-gray-500 font-light">Ngày đặt: 31/01/2026</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[14px] font-semibold text-gray-900 mb-1">$89.00</p>
                                        <span class="inline-block px-3 py-1 bg-[#ffb800] text-white text-[11px] font-medium rounded-full">
                                            Đang xử lý
                                        </span>
                                    </div>
                                </div>

                                <!-- Order Item 2 -->
                                <div class="flex items-center justify-between border-b border-gray-50 pb-6 last:border-0 last:pb-0">
                                    <div>
                                        <p class="text-[14px] font-medium text-gray-900 mb-1">Đơn hàng #1</p>
                                        <p class="text-[12px] text-gray-500 font-light">Ngày đặt: 31/01/2026</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[14px] font-semibold text-gray-900 mb-1">$99.00</p>
                                        <span class="inline-block px-3 py-1 bg-[#ffb800] text-white text-[11px] font-medium rounded-full">
                                            Đang xử lý
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hỗ trợ khách hàng -->
                        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100/50">
                            <h3 class="text-base font-semibold text-gray-900 mb-4 tracking-wide">Hỗ trợ khách hàng</h3>
                            
                            <div class="border border-gray-100 rounded-lg divide-y divide-gray-100">
                                <!-- Support Link 1 -->
                                <a href="#" class="flex items-center justify-between py-4 px-4 hover:bg-gray-50 transition-colors group first:rounded-t-lg">
                                    <div class="flex items-center gap-4 text-gray-700">
                                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                        <span class="text-[14px] font-medium group-hover:text-black">Tìm kiếm cửa hàng</span>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"></path></svg>
                                </a>

                                <!-- Support Link 2 -->
                                <a href="#" class="flex items-center justify-between py-4 px-4 hover:bg-gray-50 transition-colors group">
                                    <div class="flex items-center gap-4 text-gray-700">
                                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <span class="text-[14px] font-medium group-hover:text-black">Chính sách mua hàng Lumiere</span>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"></path></svg>
                                </a>

                                <!-- Support Link 3 -->
                                <a href="#" class="flex items-center justify-between py-4 px-4 hover:bg-gray-50 transition-colors group last:rounded-b-lg">
                                    <div class="flex items-center gap-4 text-gray-700">
                                        <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="text-[14px] font-medium group-hover:text-black">Sổ địa chỉ nhận hàng</span>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Code Edit Info -->
                    <div x-show="currentTab === 'edit-info'" style="display: none;" x-transition class="bg-white rounded-lg shadow-sm p-6 lg:p-10 border border-gray-100/50">
                        <button @click="currentTab = 'dashboard'" type="button" class="text-[13px] text-gray-500 hover:text-black mb-6 flex items-center gap-1 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Quay lại Tổng quan
                        </button>
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <!-- Tab 3: Edit Password -->
                    <div x-show="currentTab === 'edit-password'" style="display: none;" x-transition class="bg-white rounded-lg shadow-sm p-6 lg:p-10 border border-gray-100/50">
                        <button @click="currentTab = 'dashboard'" type="button" class="text-[13px] text-gray-500 hover:text-black mb-6 flex items-center gap-1 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Quay lại Tổng quan
                        </button>
                        @include('profile.partials.update-password-form')
                    </div>

                    <!-- Tab 4: Delete Account -->
                    <div x-show="currentTab === 'delete-account'" style="display: none;" x-transition class="bg-white rounded-lg shadow-sm p-6 lg:p-10 border border-gray-100/50">
                        <button @click="currentTab = 'dashboard'" type="button" class="text-[13px] text-gray-500 hover:text-black mb-6 flex items-center gap-1 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Quay lại Tổng quan
                        </button>
                        @include('profile.partials.delete-user-form')
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
