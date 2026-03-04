<x-app-layout>
<div class="font-[Inter] bg-white text-gray-900 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-x-16 items-stretch">
            
            <!-- Left Image -->
            <div class="hidden md:block relative w-full h-[700px]">
                <img src="{{ asset('user/img/image-login.jpg') }}" 
                     alt="Đăng nhập Lumiere" 
                     class="absolute inset-0 w-full h-full object-cover">
            </div>

            <!-- Right Form -->
            <div class="flex items-center justify-center py-8">
                <div class="w-full max-w-[420px]">
            
                    <!-- Title -->
                    <h1 class="text-3xl font-light uppercase text-center mb-10 tracking-wide">
                        ĐĂNG NHẬP
                    </h1>

                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Email -->
                        <div>
                            <input type="email" 
                                   name="email"
                                   placeholder="Email"
                                   value="{{ old('email') }}"
                                   required autofocus
                                   class="w-full border border-gray-300 bg-white px-4 py-4 text-[14px] font-light 
                                          focus:outline-none focus:border-gray-600 focus:ring-0 
                                          placeholder-gray-400 transition">
                            <x-input-error :messages="$errors->get('email')" 
                                           class="mt-2 text-red-500 text-sm" />
                        </div>

                        <!-- Password -->
                        <div class="relative">
                            <input type="password" 
                                   name="password"
                                   placeholder="Mật Khẩu"
                                   required
                                   class="w-full border border-gray-300 bg-white px-4 py-4 text-[14px] font-light 
                                          focus:outline-none focus:border-gray-600 focus:ring-0 
                                          placeholder-gray-400 pr-10 transition">

                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 5C5.6 5 2 12 2 12C2 12 5.6 19 12 19C18.4 19 22 12 22 12C22 12 18.4 5 12 5Z"
                                          stroke="currentColor" stroke-width="1.5"/>
                                    <circle cx="12" cy="12" r="3"
                                            stroke="currentColor" stroke-width="1.5"/>
                                </svg>
                            </span>

                            <x-input-error :messages="$errors->get('password')" 
                                           class="mt-2 text-red-500 text-sm" />
                        </div>

                        <!-- Remember + Forgot -->
                        <div class="flex items-center justify-between text-[13px] text-gray-600 font-light">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" 
                                       name="remember"
                                       class="border-gray-300 focus:ring-gray-500">
                                Ghi nhớ tôi
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="hover:text-black hover:underline">
                                    Quên mật khẩu?
                                </a>
                            @endif
                        </div>

                        <!-- Button -->
                        <div class="pt-2">
                            <button type="submit"
                                class="w-full text-white text-[14px] font-medium uppercase py-4 
                                       hover:opacity-90 transition"
                                style="background-color: #5C6955;">
                                ĐĂNG NHẬP
                            </button>
                        </div>
                    </form>

                    <!-- Register Link -->
                    <div class="mt-8 flex items-center justify-center gap-2 text-[14px] font-light text-gray-600">
                        <span>Chưa Có Tài Khoản?</span>
                        <a href="{{ route('register') }}" 
                           class="hover:text-black">
                            Đăng Ký
                        </a>
                    </div>

                    <!-- Social -->
                    <div class="mt-8 text-center text-gray-500">
                        <p class="text-[12px] mb-6">Or</p>

                        <div class="flex items-center justify-center gap-6">
                            <button class="w-10 h-10 flex items-center justify-center hover:opacity-80 transition">
                                <img src="{{ asset('user/img/Icons-apple.svg') }}" class="w-8 h-8">
                            </button>
                            <button class="w-10 h-10 flex items-center justify-center hover:opacity-80 transition">
                                <img src="{{ asset('user/img/Gmail.svg') }}" class="w-8 h-8">
                            </button>
                            <button class="w-10 h-10 flex items-center justify-center hover:opacity-80 transition">
                                <img src="{{ asset('user/img/Icons-facbook.svg') }}" class="w-8 h-8">
                            </button>
                        </div>
                    </div>

                    <!-- Policy -->
                    <div class="mt-8 text-center text-[11px] text-gray-500 leading-relaxed">
                        Khi Đăng Nhập, Bạn Đồng Ý Với 
                        <a href="#" class="underline hover:text-black">
                            Điều Khoản & Chính Sách Bảo Mật
                        </a>.
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>