<x-app-layout>
    <div class="font-[Montserrat] min-h-screen py-10" style="background: #f7f8f9;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; align-items: start;">

                <!-- Cột trái -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #f3f4f6;">
                        <div style="width: 64px; height: 64px; border-radius: 8px; background: #f9fafb; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 600; color: #1f2937; text-transform: uppercase;">
                            {{ substr(Auth::user()->full_name ?? Auth::user()->name ?? 'N', 0, 1) }}
                        </div>
                        <div>
                            <h2 style="font-size: 17px; font-weight: 600; color: #111827; margin: 0;">{{ mb_convert_case(Auth::user()->full_name ?? Auth::user()->name, MB_CASE_TITLE, "UTF-8") }}</h2>
                            <p style="font-size: 13px; color: #9ca3af; margin: 4px 0 0 0;">{{ Auth::user()->email }}</p>
                        </div>
                    </div>

                    <div style="font-size: 13px; color: #6b7280; margin-bottom: 2rem; line-height: 2;">
                        <p><span style="font-weight: 500; color: #111827;">ID:</span> {{ Auth::user()->id }}</p>
                        <p><span style="font-weight: 500; color: #111827;">Username:</span> {{ Auth::user()->email }}</p>
                        <p><span style="font-weight: 500; color: #111827;">Admin:</span> {{ Auth::user()->role_id == 1 ? 'Có' : 'Không' }}</p>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <a href="{{ route('profile.info') }}" 
                           style="display: block; width: 100%; padding: 10px 16px; text-align: center; font-size: 13px; font-weight: 500; border-radius: 6px; border: 1.5px solid #e5e7eb; color: #374151; background: white; text-decoration: none;">
                            Chỉnh sửa thông tin
                        </a>
                        <a href="{{ route('profile.password') }}" 
                           style="display: block; width: 100%; padding: 10px 16px; text-align: center; font-size: 13px; font-weight: 500; border-radius: 6px; border: 1.5px solid #4a84f5; color: #4a84f5; background: #eff6ff; text-decoration: none;">
                            ← Đổi mật khẩu
                        </a>
                        <a href="{{ route('profile.edit') }}" 
                           style="display: block; width: 100%; padding: 10px 16px; text-align: center; font-size: 13px; font-weight: 500; border-radius: 6px; border: 1.5px solid #d1d5db; color: #6b7280; background: white; text-decoration: none;">
                            ← Quay lại Hồ sơ
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" style="display: block; width: 100%; padding: 10px 16px; text-align: center; font-size: 13px; font-weight: 500; border-radius: 6px; border: none; color: white; cursor: pointer; background-color: #e53e3e;">
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Cột phải: Form đổi mật khẩu -->
                <div class="bg-white rounded-lg shadow-sm p-8 border border-gray-100">
                    @include('profile.partials.update-password-form')
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
