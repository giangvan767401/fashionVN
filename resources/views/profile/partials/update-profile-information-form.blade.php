<section>
    <header style="margin-bottom: 2rem; padding-bottom: 1.25rem; border-bottom: 1px solid #f0f0f0;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
            <div style="width: 4px; height: 20px; background: #4a84f5; border-radius: 2px;"></div>
            <h2 style="font-size: 15px; font-weight: 600; color: #1a1a1a; margin: 0;">Chỉnh sửa thông tin</h2>
        </div>
        <p style="font-size: 13px; color: #9ca3af; margin: 4px 0 0 14px; font-weight: 300;">Cập nhật thông tin hồ sơ của bạn.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem 3rem;">
            <!-- Tên -->
            <div>
                <label for="full_name" style="display: block; font-size: 13px; color: #374151; margin-bottom: 8px; font-weight: 500;">Họ và tên</label>
                <input id="full_name" name="full_name" type="text"
                    style="display: block; width: 100%; border: 1px solid #e5e7eb; border-radius: 8px; color: #111827; font-size: 14px; padding: 10px 16px; outline: none; box-sizing: border-box;"
                    value="{{ old('full_name', $user->full_name ?? $user->name) }}" required autofocus autocomplete="name" placeholder="Họ và tên">
                <x-input-error class="mt-2" :messages="$errors->get('full_name')" />
            </div>

            <!-- Email -->
            <div>
                <label for="email" style="display: block; font-size: 13px; color: #374151; margin-bottom: 8px; font-weight: 500;">Địa chỉ Email</label>
                <input id="email" name="email" type="email"
                    style="display: block; width: 100%; border: 1px solid #e5e7eb; border-radius: 8px; color: #111827; font-size: 14px; padding: 10px 16px; outline: none; box-sizing: border-box; background: #f9fafb;"
                    value="{{ old('email', $user->email) }}" autocomplete="username" placeholder="Địa chỉ Email" readonly>
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <!-- Số điện thoại -->
            <div>
                <label for="phone" style="display: block; font-size: 13px; color: #374151; margin-bottom: 8px; font-weight: 500;">Số điện thoại</label>
                <input id="phone" name="phone" type="text"
                    style="display: block; width: 100%; border: 1px solid #e5e7eb; border-radius: 8px; color: #111827; font-size: 14px; padding: 10px 16px; outline: none; box-sizing: border-box;"
                    value="{{ old('phone', $user->phone ?? '') }}" autocomplete="tel" placeholder="Số điện thoại">
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>

            <!-- Giới tính -->
            <div>
                <label for="gender" style="display: block; font-size: 13px; color: #374151; margin-bottom: 8px; font-weight: 500;">Giới tính</label>
                <select id="gender" name="gender"
                    style="display: block; width: 100%; border: 1px solid #e5e7eb; border-radius: 8px; color: #111827; font-size: 14px; padding: 10px 16px; outline: none; box-sizing: border-box; background: white;">
                    <option value="">Chọn giới tính</option>
                    <option value="male" {{ old('gender', $user->gender ?? '') === 'male' ? 'selected' : '' }}>Nam</option>
                    <option value="female" {{ old('gender', $user->gender ?? '') === 'female' ? 'selected' : '' }}>Nữ</option>
                    <option value="other" {{ old('gender', $user->gender ?? '') === 'other' ? 'selected' : '' }}>Khác</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('gender')" />
            </div>

            <!-- Ngày sinh -->
            <div>
                <label for="date_of_birth" style="display: block; font-size: 13px; color: #374151; margin-bottom: 8px; font-weight: 500;">Ngày sinh</label>
                <input id="date_of_birth" name="date_of_birth" type="date"
                    style="display: block; width: 100%; border: 1px solid #e5e7eb; border-radius: 8px; color: #111827; font-size: 14px; padding: 10px 16px; outline: none; box-sizing: border-box;"
                    value="{{ old('date_of_birth', $user->date_of_birth ? \Illuminate\Support\Carbon::parse($user->date_of_birth)->format('Y-m-d') : '') }}">
                <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
            </div>
        </div>

        <!-- Submit row -->
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #f3f4f6; display: flex; align-items: center; gap: 1rem;">
            <button type="submit" style="padding: 10px 32px; background: #111827; color: white; font-size: 13px; font-weight: 500; border-radius: 8px; border: none; cursor: pointer;">
                Lưu thay đổi
            </button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                    style="font-size: 13px; color: #16a34a;">
                    ✓ Đã lưu thành công.
                </p>
            @endif
        </div>
    </form>
</section>
