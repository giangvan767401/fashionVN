<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Giao Hàng – Lumiere</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        input, select { outline: none; }
        input:focus, select:focus { border-color: #9ca3af !important; }
        .form-input {
            width: 100%; padding: 11px 14px; border: 1px solid #e5e7eb;
            border-radius: 6px; font-size: 13px; color: #374151;
            font-family: 'Montserrat', sans-serif; box-sizing: border-box;
        }
        .form-input::placeholder { color: #9ca3af; }
    </style>
</head>
<body class="bg-white min-h-screen">

<div style="display: grid; grid-template-columns: 1fr 420px; min-height: 100vh;">

    <!-- ===== LEFT SIDE ===== -->
    <div style="display: flex; justify-content: center; align-items: flex-start;">
    <div style="padding: 48px 56px; max-width: 640px; width: 100%;">

        <!-- Logo -->
        <a href="{{ route('home') }}" style="text-decoration: none; display: inline-block; margin-bottom: 40px;">
            <div style="font-family: 'Cormorant Garamond', serif; font-size: 24px; font-weight: 600; color: #1a1a1a; letter-spacing: 0.1em;">
                Lumiere<span style="color: #6b8c5e; font-size: 9px; margin-left: 2px;">●</span>
            </div>
            <div style="font-size: 9px; letter-spacing: 0.3em; color: #9ca3af; text-transform: uppercase; margin-top: 1px;">women clothing</div>
        </a>

        <!-- Breadcrumb Steps -->
        <div style="display: flex; align-items: center; gap: 10px; font-size: 13px; margin-bottom: 40px;">
            <a href="{{ route('cart.index') }}" style="color: #9ca3af; text-decoration: none;">Giỏ Hàng</a>
            <span style="color: #d1d5db;">/</span>
            <span style="color: #111827; font-weight: 600;">Thông Tin</span>
            <span style="color: #d1d5db;">/</span>
            <span style="color: #c4c4c4;">Vận Chuyển</span>
            <span style="color: #d1d5db;">/</span>
            <span style="color: #c4c4c4;">Thanh Toán</span>
        </div>

        <form method="POST" action="{{ route('checkout.store_info') }}">
            @csrf

            <!-- Liên Hệ -->
            <div style="margin-bottom: 32px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <h2 style="font-size: 15px; font-weight: 600; color: #111827; margin: 0;">Liên Hệ</h2>
                    <span style="font-size: 12px; color: #6b7280;">Bạn Đã Có Tài Khoản?
                        <a href="{{ route('login') }}" style="color: #374151; font-weight: 500; text-decoration: underline;">Đăng Nhập</a>
                    </span>
                </div>
                <input type="email" name="email" class="form-input @error('email') border-red-500 @enderror" placeholder="Địa Chỉ Email"
                    value="{{ old('email', $user->email ?? '') }}"
                    style="padding-left: 38px; background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'14\' height=\'14\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%239ca3af\' stroke-width=\'2\'%3E%3Cpath d=\'M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2z\'/%3E%3Cpolyline points=\'22,6 12,13 2,6\'/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: 12px center;">
                @error('email')<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                <div style="margin-top: 10px; display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" id="newsletter" name="newsletter" style="width: 14px; height: 14px; accent-color: #4a5845;">
                    <label for="newsletter" style="font-size: 12px; color: #6b7280; cursor: pointer;">Đăng Ký Nhận Ưu Đãi & Tin Tức Qua Email</label>
                </div>
            </div>

            <!-- Địa Chỉ Nhận Hàng -->
            <div style="margin-bottom: 32px;">
                <h2 style="font-size: 15px; font-weight: 600; color: #111827; margin: 0 0 14px 0;">Địa Chỉ Nhận Hàng</h2>

                <!-- Khu Vực -->
                <div style="position: relative; margin-bottom: 10px;">
                    <select name="province" class="form-input" style="appearance: none; padding-right: 36px; cursor: pointer;">
                        @php $prov = old('province', session('checkout.info.province') ?? ($savedAddress->province ?? '')); @endphp
                        <option value="">Khu Vực</option>
                        <option {{ $prov == 'Hà Nội' ? 'selected' : '' }}>Hà Nội</option>
                        <option {{ $prov == 'Hồ Chí Minh' ? 'selected' : '' }}>Hồ Chí Minh</option>
                        <option {{ $prov == 'Đà Nẵng' ? 'selected' : '' }}>Đà Nẵng</option>
                        <option {{ $prov == 'Cần Thơ' ? 'selected' : '' }}>Cần Thơ</option>
                        <option {{ $prov == 'Hải Phòng' ? 'selected' : '' }}>Hải Phòng</option>
                        <option {{ $prov == 'Bình Dương' ? 'selected' : '' }}>Bình Dương</option>
                        <option {{ $prov == 'Đồng Nai' ? 'selected' : '' }}>Đồng Nai</option>
                    </select>
                    <svg style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none;" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>

                @php
                    $names = explode(' ', $savedAddress->recipient_name ?? $user->full_name ?? '');
                    $defLast = count($names) > 1 ? array_shift($names) : '';
                    $defFirst = implode(' ', $names);
                @endphp
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                    <div>
                        <input type="text" name="last_name" class="form-input @error('last_name') border-red-500 @enderror" placeholder="Họ" value="{{ old('last_name', session('checkout.info.last_name') ?? $defLast) }}">
                        @error('last_name')<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <input type="text" name="first_name" class="form-input @error('first_name') border-red-500 @enderror" placeholder="Tên" value="{{ old('first_name', session('checkout.info.first_name') ?? $defFirst) }}">
                        @error('first_name')<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                    </div>
                </div>

                <!-- Công Ty -->
                <input type="text" name="company" class="form-input" placeholder="Công Ty (Tùy Chọn)" value="{{ old('company', session('checkout.info.company')) }}" style="margin-bottom: 10px;">

                <!-- Địa Chỉ -->
                <div style="position: relative; margin-bottom: 10px;">
                    @php 
                        $rawAddr = session('checkout.info.address') ?? ($savedAddress->street_address ?? '');
                        // remove apartment part if saved like "address (apt)"
                        $addrParts = explode(' (', $rawAddr);
                        $defAddr = $addrParts[0];
                    @endphp
                    <input type="text" name="address" class="form-input @error('address') border-red-500 @enderror" placeholder="Địa Chỉ" value="{{ old('address', $defAddr) }}" style="padding-right: 36px;">
                    <svg style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af;" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    @error('address')<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                </div>

                <!-- Căn Hộ -->
                <input type="text" name="apartment" class="form-input" placeholder="Căn Hộ, Phòng, V.V. (Không Bắt Buộc)" value="{{ old('apartment', session('checkout.info.apartment') ?? (isset($addrParts[1]) ? rtrim($addrParts[1], ')') : '')) }}" style="margin-bottom: 10px;">

                <!-- Mã Bưu Điện / Thành Phố -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                    <input type="text" name="postal_code" class="form-input" placeholder="Mã Bưu Điện" value="{{ old('postal_code', session('checkout.info.postal_code') ?? ($savedAddress->postal_code ?? '')) }}">
                    <div>
                        <input type="text" name="city" class="form-input @error('city') border-red-500 @enderror" placeholder="Thành Phố" value="{{ old('city', session('checkout.info.city') ?? '') }}">
                        @error('city')<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                    </div>
                </div>

                <!-- Số Điện Thoại -->
                <div style="position: relative; margin-bottom: 14px;">
                    <input type="tel" name="phone" class="form-input @error('phone') border-red-500 @enderror" placeholder="Số Điện Thoại" value="{{ old('phone', session('checkout.info.phone') ?? ($savedAddress->recipient_phone ?? ($user->phone ?? ''))) }}" style="padding-right: 36px;">
                    <svg style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%);" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81 19.79 19.79 0 01.01 1.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.16 6.16l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                    @error('phone')<span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                </div>

                <!-- Save Checkbox -->
                <div style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" id="save_info" name="save_info" style="width: 14px; height: 14px; accent-color: #4a5845;">
                    <label for="save_info" style="font-size: 12px; color: #6b7280; cursor: pointer;">Lưu Thông Tin Này Cho Lần Sau</label>
                </div>
            </div>

            <!-- Buttons -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 40px; padding-top: 24px; border-top: 1px solid #f3f4f6;">
                <a href="{{ route('cart.index') }}" style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: #6b7280; text-decoration: none;">
                    &#8249; Quay Lại
                </a>
                <button type="submit"
                    style="padding: 13px 48px; background: #4a5845; color: white; font-size: 14px; font-weight: 500; border: none; border-radius: 4px; cursor: pointer; letter-spacing: 0.04em;">
                    Tiếp Tục
                </button>
            </div>
        </form>
    </div>
    </div>

    <!-- ===== RIGHT SIDE: Cart Summary ===== -->
    <div style="background: #f7f7f5; border-left: 1px solid #efefef; padding: 48px 36px; position: sticky; top: 0; height: 100vh; overflow-y: auto; box-sizing: border-box;">
        <h2 style="font-size: 15px; font-weight: 600; color: #111827; text-align: center; margin: 0 0 28px 0;">Giỏ Hàng Của Bạn</h2>

        <!-- Product List -->
        <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 28px;">
            @foreach($cartItems as $item)
            @php
                $product = $item->variant->product ?? null;
                $primaryImage = $product?->images->where('is_primary', true)->first() ?? $product?->images->first();
                $imageUrl = asset('user/img/default-product.jpg');
                if ($primaryImage) {
                    $imageUrl = \Illuminate\Support\Str::startsWith($primaryImage->url, 'http') 
                        ? $primaryImage->url 
                        : (\Illuminate\Support\Str::startsWith($primaryImage->url, 'images/') ? asset($primaryImage->url) : asset('storage/' . $primaryImage->url));
                }
                $size = null; $color = null;
                foreach ($item->variant->attributeValues as $attr) {
                    $g = mb_strtolower(optional($attr->group)->name ?? '', 'UTF-8');
                    if (str_contains($g, 'kích')) $size = $attr->value;
                    if (str_contains($g, 'màu')) $color = $attr->value;
                }
            @endphp
            <div style="display: flex; align-items: flex-start; gap: 14px;">
                <!-- Image with quantity badge -->
                <div style="position: relative; flex-shrink: 0;">
                    <img src="{{ $imageUrl }}" alt="{{ $product?->name }}"
                         style="width: 70px; height: 86px; object-fit: cover; border-radius: 4px; border: 1px solid #e5e7eb;">
                    <span style="position: absolute; top: -8px; right: -8px; background: #9ca3af; color: white; font-size: 10px; font-weight: 600; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">{{ $item->quantity }}</span>
                </div>
                <!-- Info -->
                <div style="flex: 1;">
                    <p style="font-size: 13px; font-weight: 600; color: #111827; margin: 0 0 4px 0;">{{ $product?->name }}</p>
                    @if($size)<p style="font-size: 11px; color: #6b7280; margin: 0 0 2px 0;">Kích Cỡ: {{ $size }}</p>@endif
                    @if($color)<p style="font-size: 11px; color: #6b7280; margin: 0 0 8px 0;">Màu Sắc: {{ $color }}</p>@endif
                    <!-- Mini quantity controls -->
                    <div style="display: flex; align-items: center; gap: 0; border: 1px solid #d1d5db; border-radius: 2px; width: fit-content; background: white;">
                        <form method="POST" action="{{ route('cart.update', $item->id) }}" style="margin:0;">
                            @csrf <input type="hidden" name="action" value="decrease">
                            <button type="submit" style="width: 24px; height: 24px; border: none; background: transparent; cursor: pointer; font-size: 14px; color: #374151;">−</button>
                        </form>
                        <span style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #111827; border-left: 1px solid #e5e7eb; border-right: 1px solid #e5e7eb;">{{ $item->quantity }}</span>
                        <form method="POST" action="{{ route('cart.update', $item->id) }}" style="margin:0;">
                            @csrf <input type="hidden" name="action" value="increase">
                            <button type="submit" style="width: 24px; height: 24px; border: none; background: transparent; cursor: pointer; font-size: 14px; color: #374151;">+</button>
                        </form>
                    </div>
                </div>
                <!-- Price -->
                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                    <form method="POST" action="{{ route('cart.remove', $item->id) }}" style="margin: 0;">
                        @csrf @method('DELETE')
                        <button type="submit" style="background: none; border: none; cursor: pointer; color: #9ca3af; font-size: 16px; line-height: 1; padding: 0;">×</button>
                    </form>
                    <span style="font-size: 14px; font-weight: 600; color: #111827; margin-top: 4px;">$ {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Divider -->
        <div style="border-top: 1px solid #e5e7eb; margin-bottom: 20px;"></div>

        <!-- Order Summary -->
        @php $tax = $cartTotal * 0.08; $total = $cartTotal + $tax; @endphp
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <div style="display: flex; justify-content: space-between;">
                <span style="font-size: 13px; color: #6b7280;">Tạm Tính ({{ $cartItems->count() }})</span>
                <span style="font-size: 13px; font-weight: 600;">{{ number_format($cartTotal, 0, ',', '.') }}đ</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="font-size: 13px; color: #6b7280;">Thuế</span>
                <span style="font-size: 13px; font-weight: 600;">{{ number_format($tax, 0, ',', '.') }}đ</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="font-size: 13px; color: #6b7280;">Phí Vận Chuyển</span>
                <span style="font-size: 13px; font-weight: 600; color: #4a7c59;">Miễn Phí</span>
            </div>
            <div style="border-top: 1px solid #e5e7eb; padding-top: 12px; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 15px; font-weight: 600; color: #111827;">Tổng Đơn Hàng:</span>
                <span style="font-size: 16px; font-weight: 700; color: #111827;">{{ number_format($total, 0, ',', '.') }}đ</span>
            </div>
            <p style="font-size: 11px; color: #9ca3af; line-height: 1.6; margin: 0;">
                Tổng số tiền bạn thanh toán đã bao gồm toàn bộ thuế và phí hải quan áp dụng. Chúng tôi cam kết không thu thêm bất kỳ khoản phí nào khi giao hàng.
            </p>
        </div>
    </div>

</div>

</body>
</html>
