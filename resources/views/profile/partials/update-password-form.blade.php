<section>
    <header style="margin-bottom: 2rem; padding-bottom: 1.25rem; border-bottom: 1px solid #f0f0f0;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
            <div style="width: 4px; height: 20px; background: #e53e3e; border-radius: 2px;"></div>
            <h2 style="font-size: 15px; font-weight: 600; color: #1a1a1a; margin: 0;">Đổi mật khẩu</h2>
        </div>
        <p style="font-size: 13px; color: #9ca3af; margin: 4px 0 0 14px; font-weight: 300;">Đảm bảo tài khoản của bạn được bảo vệ bằng mật khẩu mạnh.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 480px;">
            <!-- Mật khẩu hiện tại -->
            <div>
                <label for="update_password_current_password" style="display: block; font-size: 13px; color: #374151; margin-bottom: 8px; font-weight: 500;">Mật khẩu hiện tại</label>
                <input id="update_password_current_password" name="current_password" type="password"
                    style="display: block; width: 100%; border: 1px solid #e5e7eb; border-radius: 8px; color: #111827; font-size: 14px; padding: 10px 16px; outline: none; box-sizing: border-box;"
                    autocomplete="current-password" placeholder="Nhập mật khẩu hiện tại">
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <!-- Mật khẩu mới -->
            <div>
                <label for="update_password_password" style="display: block; font-size: 13px; color: #374151; margin-bottom: 8px; font-weight: 500;">Mật khẩu mới</label>
                <input id="update_password_password" name="password" type="password"
                    style="display: block; width: 100%; border: 1px solid #e5e7eb; border-radius: 8px; color: #111827; font-size: 14px; padding: 10px 16px; outline: none; box-sizing: border-box;"
                    autocomplete="new-password" placeholder="Nhập mật khẩu mới">
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <!-- Xác nhận mật khẩu mới -->
            <div>
                <label for="update_password_password_confirmation" style="display: block; font-size: 13px; color: #374151; margin-bottom: 8px; font-weight: 500;">Xác nhận mật khẩu mới</label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                    style="display: block; width: 100%; border: 1px solid #e5e7eb; border-radius: 8px; color: #111827; font-size: 14px; padding: 10px 16px; outline: none; box-sizing: border-box;"
                    autocomplete="new-password" placeholder="Nhập lại mật khẩu mới">
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <!-- Submit row -->
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #f3f4f6; display: flex; align-items: center; gap: 1rem;">
            <button type="submit" style="padding: 10px 32px; background: #111827; color: white; font-size: 13px; font-weight: 500; border-radius: 8px; border: none; cursor: pointer;">
                Cập nhật mật khẩu
            </button>
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                    style="font-size: 13px; color: #16a34a;">
                    ✓ Mật khẩu đã được cập nhật.
                </p>
            @endif
        </div>
    </form>
</section>
