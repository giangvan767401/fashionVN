<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán – Lumiere</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .info-row {
            display: flex; justify-content: space-between; align-items: flex-start;
            padding: 16px 0; border-bottom: 1px solid #f3f4f6;
        }
        .info-label { width: 100px; font-size: 13px; color: #6b7280; font-weight: 500; }
        .info-value { flex: 1; font-size: 13px; color: #111827; }
        .info-action { font-size: 12px; color: #6b7280; text-decoration: underline; }
        
        .radio-box {
            display: flex; align-items: center; gap: 12px; padding: 16px 20px; border: 1px solid #4a5845;
            border-radius: 6px; cursor: pointer; background: #fafaf9;
        }
        input[type="radio"] {
            margin-top: 2px; width: 16px; height: 16px; accent-color: #4a5845;
        }
    </style>
</head>
<body class="bg-white min-h-screen">

<div style="display: grid; grid-template-columns: 1fr 420px; min-height: 100vh;">

    <!-- ===== LEFT SIDE ===== -->
    <div style="padding: 48px 56px; max-width: 640px;">

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
            <a href="{{ route('checkout') }}" style="color: #9ca3af; text-decoration: none;">Thông Tin</a>
            <span style="color: #d1d5db;">/</span>
            <a href="{{ route('checkout.shipping') }}" style="color: #9ca3af; text-decoration: none;">Vận Chuyển</a>
            <span style="color: #d1d5db;">/</span>
            <span style="color: #111827; font-weight: 600;">Thanh Toán</span>
        </div>

        <!-- Info Summary Box -->
        <div style="border: 1px solid #e5e7eb; border-radius: 6px; padding: 0 20px; margin-bottom: 40px;">
            <div class="info-row">
                <span class="info-label">Liên Hệ</span>
                <span class="info-value">{{ session('checkout.info.email') }}</span>
                <a href="{{ route('checkout') }}" class="info-action">Thay Đổi</a>
            </div>
            <div class="info-row">
                <span class="info-label">Đến</span>
                <span class="info-value">
                    {{ session('checkout.info.address') }},
                    {{ session('checkout.info.city') ? session('checkout.info.city') . ',' : '' }} 
                    {{ session('checkout.info.province') }}
                </span>
                <a href="{{ route('checkout') }}" class="info-action">Thay Đổi</a>
            </div>
            <div class="info-row" style="border-bottom: none;">
                <span class="info-label">Phương Thức</span>
                <span class="info-value">
                    {{ session('checkout.shipping_method') === 'express' || session('checkout.shipping_fee') > 0 ? 'Giao Hàng Nhanh' : 'Chuyển Phát Tiêu Chuẩn' }}
                    · <span style="font-weight: 600;">{{ session('checkout.shipping_fee') > 0 ? number_format(session('checkout.shipping_fee'), 0, ',', '.') . 'đ' : 'Miễn Phí' }}</span>
                </span>
                <a href="{{ route('checkout.shipping') }}" class="info-action">Thay Đổi</a>
            </div>
        </div>

        <form method="POST" action="{{ route('checkout.store_payment') }}">
            @csrf
            
            <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin-bottom: 4px;">Thanh Toán</h2>
            <p style="font-size: 13px; color: #6b7280; margin-bottom: 24px;">Tất cả giao dịch đều được bảo mật và mã hóa.</p>
            
            <!-- Payment Methods -->
            <label class="radio-box">
                <input type="radio" name="payment_method" value="cod" checked>
                <div style="flex: 1; display: flex; flex-direction: column;">
                    <span style="font-size: 14px; font-weight: 600; color: #111827;">Thanh Toán Khi Nhận Hàng (COD)</span>
                    <span style="font-size: 12px; color: #6b7280; margin-top: 4px;">Bạn sẽ thanh toán bằng tiền mặt khi đơn hàng được giao đến.</span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4a5845" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
            </label>

            <!-- Buttons -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 40px; padding-top: 24px;">
                <a href="{{ route('checkout.shipping') }}" style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: #6b7280; text-decoration: none;">
                    &#8249; Quay Lại Vận Chuyển
                </a>
                <button type="submit" style="padding: 13px 48px; background: #4a5845; color: white; font-size: 14px; font-weight: 500; border: none; border-radius: 4px; cursor: pointer; letter-spacing: 0.04em;">
                    Hoàn Tất Đơn Hàng
                </button>
            </div>
        </form>
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
                <div style="position: relative; flex-shrink: 0;">
                    <img src="{{ $imageUrl }}" alt="{{ $product?->name }}" style="width: 60px; height: 80px; object-fit: contain; border-radius: 4px; border: 1px solid #e5e7eb;">
                    <span style="position: absolute; top: -8px; right: -8px; background: white; border: 1px solid #e5e7eb; color: #374151; font-size: 10px; font-weight: 600; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">{{ $item->quantity }}</span>
                </div>
                <div style="flex: 1; padding-top: 4px;">
                    <p style="font-size: 13px; font-weight: 600; color: #111827; margin: 0 0 4px 0;">{{ $product?->name }}</p>
                    @if($size)<p style="font-size: 11px; color: #6b7280; margin: 0 0 2px 0;">Kích Cỡ: {{ $size }}</p>@endif
                    @if($color)<p style="font-size: 11px; color: #6b7280; margin: 0 0 8px 0;">Màu Sắc: {{ $color }}</p>@endif
                </div>
                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px; padding-top: 4px;">
                    <span style="font-size: 14px; font-weight: 600; color: #111827;">{{ number_format($item->unit_price, 0, ',', '.') }}đ</span>
                </div>
            </div>
            @endforeach
        </div>

        <div style="border-top: 1px solid #e5e7eb; margin-bottom: 20px;"></div>

        <!-- Order Summary -->
        @php 
            $tax = $cartTotal * 0.08; 
            $total = $cartTotal + $tax + $shippingFee; 
        @endphp
        <div style="display: flex; flex-direction: column; gap: 14px;">
            <div style="display: flex; justify-content: space-between;">
                <span style="font-size: 13px; color: #6b7280;">Tạm Tính ({{ $cartItems->count() }})</span>
                <span style="font-size: 13px; color: #374151;">{{ number_format($cartTotal, 0, ',', '.') }}đ</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="font-size: 13px; color: #6b7280;">Thuế</span>
                <span style="font-size: 13px; color: #374151;">{{ number_format($tax, 0, ',', '.') }}đ</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="font-size: 13px; color: #6b7280;">Phí Vận Chuyển</span>
                <span style="font-size: 13px; color: #374151;">{{ $shippingFee > 0 ? number_format($shippingFee, 0, ',', '.') . 'đ' : 'Miễn Phí' }}</span>
            </div>
            <div style="border-top: 1px solid #e5e7eb; padding-top: 16px; margin-top: 4px; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 15px; color: #111827;">Tổng Đơn Hàng:</span>
                <span style="font-size: 18px; font-weight: 700; color: #111827;">{{ number_format($total, 0, ',', '.') }}đ</span>
            </div>
        </div>
    </div>

</div>

</body>
</html>
