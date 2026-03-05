<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng – Lumiere</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        @csrf-token { content: "{{ csrf_token() }}"; }
    </style>
</head>
<body class="bg-white min-h-screen">

    <!-- Minimal Header -->
    <header style="padding: 24px 48px; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center;">
        <a href="{{ route('home') }}" style="text-decoration: none;">
            <div style="font-family: 'Cormorant Garamond', serif; font-size: 26px; font-weight: 600; color: #1a1a1a; letter-spacing: 0.1em;">
                Lumiere<span style="color: #6b8c5e; font-size: 10px; margin-left: 2px;">●</span>
            </div>
            <div style="font-size: 9px; letter-spacing: 0.3em; color: #9ca3af; text-transform: uppercase; margin-top: 2px;">women clothing</div>
        </a>
    </header>

    <main style="max-width: 1100px; margin: 0 auto; padding: 48px 32px;">

        <!-- Page Title Row with Steps -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
            <div style="display: flex; align-items: center; gap: 12px; font-size: 14px;">
                <a href="{{ url()->previous() }}" style="color: #9ca3af; text-decoration: none; font-weight: 400;">Trở Lại</a>
                <span style="color: #d1d5db;">|</span>
                <span style="font-size: 20px; font-weight: 700; color: #111827;">Giỏ Hàng</span>
                <span style="color: #d1d5db; margin: 0 4px;">/</span>
                <span style="color: #9ca3af; font-weight: 400;">Thông Tin</span>
                <span style="color: #d1d5db; margin: 0 4px;">/</span>
                <span style="color: #9ca3af; font-weight: 400;">Vận Chuyển</span>
                <span style="color: #d1d5db; margin: 0 4px;">/</span>
                <span style="color: #9ca3af; font-weight: 400;">Thanh Toán</span>
            </div>
            <a href="{{ route('collection') }}" style="font-size: 13px; color: #6b7280; text-decoration: none; letter-spacing: 0.05em;">Tiếp Tục Mua Sắm</a>
        </div>

        @if($cartItems->isEmpty())
            <!-- Empty Cart -->
            <div style="text-align: center; padding: 80px 0;">
                <p style="font-size: 16px; color: #9ca3af; margin-bottom: 24px;">Giỏ hàng của bạn đang trống.</p>
                <a href="{{ route('collection') }}" style="display: inline-block; padding: 12px 32px; background: #4a5845; color: white; font-size: 14px; text-decoration: none; letter-spacing: 0.05em;">
                    Khám phá bộ sưu tập
                </a>
            </div>
        @else

        <!-- Cart Table -->
        <div style="display: grid; grid-template-columns: 1fr 420px; gap: 64px; align-items: start;">

            <!-- Left: Product List -->
            <div>
                <!-- Table Header -->
                <div style="display: grid; grid-template-columns: 1fr 100px 110px 80px; gap: 16px; padding-bottom: 12px; border-bottom: 1px solid #e5e7eb; margin-bottom: 8px;">
                    <span style="font-size: 12px; font-weight: 500; color: #374151; letter-spacing: 0.05em;">Chi Tiết Đơn Hàng</span>
                    <span style="font-size: 12px; font-weight: 500; color: #374151; letter-spacing: 0.05em; text-align: center;">Giá</span>
                    <span style="font-size: 12px; font-weight: 500; color: #374151; letter-spacing: 0.05em; text-align: center;">Số Lượng</span>
                    <span style="font-size: 12px; font-weight: 500; color: #374151; letter-spacing: 0.05em; text-align: right;">Tổng</span>
                </div>

                <!-- Cart Items -->
                @foreach($cartItems as $item)
                @php
                    $product = $item->variant->product ?? null;
                    $primaryImage = $product?->images->where('is_primary', true)->first() ?? $product?->images->first();
                    $imageUrl = $primaryImage ? asset($primaryImage->url) : asset('user/img/default-product.jpg');
                    $size = null;
                    $color = null;
                    foreach ($item->variant->attributeValues as $attr) {
                        $groupName = mb_strtolower(optional($attr->group)->name ?? '', 'UTF-8');
                        if (str_contains($groupName, 'kích')) $size = $attr->value;
                        if (str_contains($groupName, 'màu')) $color = $attr->value;
                    }
                @endphp
                <div style="display: grid; grid-template-columns: 1fr 100px 110px 80px; gap: 16px; align-items: center; padding: 24px 0; border-bottom: 1px solid #f3f4f6;">

                    <!-- Product Info -->
                    <div style="display: flex; gap: 16px; align-items: flex-start;">
                        @if($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $product->name }}"
                             style="width: 100px; height: 120px; object-fit: contain; border-radius: 2px; flex-shrink: 0;">
                        @else
                        <div style="width: 100px; height: 120px; background: #f3f4f6; border-radius: 2px; flex-shrink: 0;"></div>
                        @endif
                        <div style="flex: 1; padding-top: 4px;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <p style="font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 8px 0;">{{ $product?->name ?? 'Sản phẩm' }}</p>
                                    @if($size)
                                    <p style="font-size: 12px; color: #6b7280; margin: 0 0 4px 0;">Kích Cỡ: {{ $size }}</p>
                                    @endif
                                    @if($color)
                                    <p style="font-size: 12px; color: #6b7280; margin: 0;">Màu Sắc: {{ $color }}</p>
                                    @endif
                                </div>
                                <!-- Remove Button -->
                                <form method="POST" action="{{ route('cart.remove', $item->id) }}" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; cursor: pointer; color: #9ca3af; font-size: 18px; padding: 0; line-height: 1;" title="Xóa">×</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Unit Price -->
                    <div style="text-align: center;">
                        <span style="font-size: 14px; color: #374151; font-weight: 500;">{{ number_format($item->unit_price, 0, ',', '.') }}đ</span>
                    </div>

                    <!-- Quantity Controls -->
                    <div style="display: flex; align-items: center; justify-content: center; gap: 0; border: 1px solid #d1d5db; border-radius: 2px; width: fit-content; margin: 0 auto;">
                        <form method="POST" action="{{ route('cart.update', $item->id) }}" style="margin: 0;">
                            @csrf
                            <input type="hidden" name="action" value="decrease">
                            <button type="submit" style="width: 32px; height: 32px; border: none; background: #f9fafb; cursor: pointer; font-size: 16px; color: #374151; display: flex; align-items: center; justify-content: center;">−</button>
                        </form>
                        <span style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 500; color: #111827; border-left: 1px solid #d1d5db; border-right: 1px solid #d1d5db;">{{ $item->quantity }}</span>
                        <form method="POST" action="{{ route('cart.update', $item->id) }}" style="margin: 0;">
                            @csrf
                            <input type="hidden" name="action" value="increase">
                            <button type="submit" style="width: 32px; height: 32px; border: none; background: #f9fafb; cursor: pointer; font-size: 16px; color: #374151; display: flex; align-items: center; justify-content: center;">+</button>
                        </form>
                    </div>

                    <!-- Item Total -->
                    <div style="text-align: right;">
                        <span style="font-size: 14px; color: #111827; font-weight: 600;">{{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}đ</span>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Right: Order Summary -->
            <div style="background: #fafafa; padding: 32px; border-radius: 4px;">
                @php
                    $tax = $cartTotal * 0.08;
                    $total = $cartTotal + $tax;
                @endphp
                <div style="space-y: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <span style="font-size: 14px; color: #6b7280;">Tạm Tính ({{ $cartItems->count() }})</span>
                        <span style="font-size: 14px; font-weight: 600; color: #111827;">{{ number_format($cartTotal, 0, ',', '.') }}đ</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <span style="font-size: 14px; color: #6b7280;">Thuế</span>
                        <span style="font-size: 14px; font-weight: 600; color: #111827;">{{ number_format($tax, 0, ',', '.') }}đ</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px solid #e5e7eb;">
                        <span style="font-size: 14px; color: #6b7280;">Phí Vận Chuyển</span>
                        <span style="font-size: 14px; font-weight: 600; color: #4a7c59;">Miễn Phí</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="font-size: 15px; font-weight: 600; color: #111827;">Tổng Đơn Hàng:</span>
                        <span style="font-size: 16px; font-weight: 700; color: #111827;">{{ number_format($total, 0, ',', '.') }}đ</span>
                    </div>
                    <p style="font-size: 11px; color: #9ca3af; line-height: 1.6; margin-bottom: 24px;">
                        Tổng số tiền bạn thanh toán đã bao gồm toàn bộ thuế và phí hải quan áp dụng. Chúng tôi cam kết không thu thêm bất kỳ khoản phí nào khi giao hàng.
                    </p>
                    <a href="{{ route('checkout') }}"
                       style="display: block; width: 100%; padding: 14px; text-align: center; background: #4a5845; color: white; font-size: 14px; font-weight: 500; text-decoration: none; letter-spacing: 0.05em; border-radius: 2px; box-sizing: border-box;">
                        Tiếp Tục
                    </a>
                </div>
            </div>

        </div>
        @endif
    </main>

</body>
</html>
