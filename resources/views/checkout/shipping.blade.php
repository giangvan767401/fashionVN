<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vận Chuyển – Lumiere</title>
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
            display: flex; align-items: flex-start; gap: 12px; padding: 16px 0; border-bottom: 1px solid #f3f4f6;
            cursor: pointer;
        }
        .radio-box:last-child { border-bottom: none; }
        input[type="radio"] {
            margin-top: 2px; width: 16px; height: 16px; accent-color: #4a5845;
        }
        .shipping-title { font-size: 14px; font-weight: 600; color: #111827; }
        .shipping-desc { font-size: 12px; color: #6b7280; margin-top: 4px; }
        .shipping-price { font-size: 14px; font-weight: 600; color: #111827; margin-left: auto; }
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
            <a href="{{ route('checkout') }}" style="color: #9ca3af; text-decoration: none;">Thông Tin</a>
            <span style="color: #d1d5db;">/</span>
            <span style="color: #111827; font-weight: 600;">Vận Chuyển</span>
            <span style="color: #d1d5db;">/</span>
            <span style="color: #c4c4c4;">Thanh Toán</span>
        </div>

        <!-- Info Summary Box -->
        <div style="border: 1px solid #e5e7eb; border-radius: 6px; padding: 0 20px; margin-bottom: 40px;">
            <div class="info-row">
                <span class="info-label">Liên Hệ</span>
                <span class="info-value">{{ session('checkout.info.email') }}</span>
                <a href="{{ route('checkout') }}" class="info-action">Thay Đổi</a>
            </div>
            <div class="info-row" style="border-bottom: none;">
                <span class="info-label">Đến</span>
                <span class="info-value">
                    {{ session('checkout.info.address') }},
                    {{ session('checkout.info.city') ? session('checkout.info.city') . ',' : '' }} 
                    {{ session('checkout.info.province') }}
                </span>
                <a href="{{ route('checkout') }}" class="info-action">Thay Đổi</a>
            </div>
        </div>

        <form method="POST" action="{{ route('checkout.store_shipping') }}">
            @csrf
            
            <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin-bottom: 16px;">Tùy Chọn Giao Hàng</h2>
            
            <div style="border: 1px solid #e5e7eb; border-radius: 6px; padding: 0 20px;">
                <!-- Standard Shipping -->
                <label class="radio-box">
                    <input type="radio" name="shipping_method" value="standard" checked>
                    <div>
                        <div class="shipping-title">Chuyển Phát Nhanh</div>
                        <div class="shipping-desc">Trong vòng 3–4 ngày làm việc</div>
                    </div>
                    <div class="shipping-price">Miễn Phí</div>
                </label>

                <!-- Express Shipping Options -->
                <div style="padding: 16px 0; border-bottom: 1px solid #f3f4f6; display: flex; gap: 12px; margin-left: 28px;">
                     <div style="width: 140px;">
                        <span style="font-size: 13px; font-weight: 600; color: #6b7280;">Ngày Giao Dự Kiến:</span>
                     </div>
                     <div style="flex: 1; display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        @php
                            $today = \Carbon\Carbon::now();
                            $days = [2, 3, 4, 5];
                            $vnDays = [
                                'Monday' => 'Thứ Hai', 'Tuesday' => 'Thứ Ba', 'Wednesday' => 'Thứ Tư',
                                'Thursday' => 'Thứ Năm', 'Friday' => 'Thứ Sáu', 'Saturday' => 'Thứ Bảy', 'Sunday' => 'Chủ Nhật'
                            ];
                        @endphp
                        @foreach(array_slice($days, 0, 4) as $index => $add)
                            @php $date = $today->copy()->addDays($add); @endphp
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #374151; cursor: pointer;">
                                <input type="radio" name="delivery_date" value="standard_{{ $add }}" {{ $index === 0 ? 'checked' : '' }} style="width:14px; height:14px;">
                                {{ $vnDays[$date->format('l')] }}, {{ $date->format('d/m') }}
                            </label>
                        @endforeach
                     </div>
                </div>

                <!-- Express Options -->
                <div style="padding: 16px 0; display: flex; gap: 12px; margin-left: 28px;">
                     <div style="width: 140px;">
                        <span style="font-size: 13px; font-weight: 600; color: #6b7280;">Đảm Bảo Trước Ngày:</span>
                     </div>
                     <div style="flex: 1; display: flex; flex-direction: column; gap: 12px;">
                        @php
                            $e1 = $today->copy()->addDays(1);
                            $e2 = $today->copy()->addDays(2);
                        @endphp
                        <label style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #374151;">
                                <input type="radio" name="delivery_date" value="express_1" style="width:14px; height:14px;">
                                {{ $vnDays[$e1->format('l')] }}, {{ $e1->format('d/m') }}
                            </div>
                            <span class="shipping-price" style="font-size: 13px;">$25.00</span>
                        </label>
                        <label style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #374151;">
                                <input type="radio" name="delivery_date" value="express_2" style="width:14px; height:14px;">
                                {{ $vnDays[$e2->format('l')] }}, {{ $e2->format('d/m') }}
                            </div>
                            <span class="shipping-price" style="font-size: 13px;">$24.00</span>
                        </label>
                     </div>
                </div>

            </div>

            <!-- Buttons -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 40px; padding-top: 24px;">
                <a href="{{ route('checkout') }}" style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: #6b7280; text-decoration: none;">
                    &#8249; Quay Lại
                </a>
                <button type="submit" style="padding: 13px 48px; background: #4a5845; color: white; font-size: 14px; font-weight: 500; border: none; border-radius: 4px; cursor: pointer; letter-spacing: 0.04em;">
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

        <!-- Coupon Form -->
        <div style="margin-top: 20px; margin-bottom: 14px; border-top: 1px solid #e5e7eb; padding-top: 16px;">
            @if(session('status'))
            <div style="background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:10px 12px; border-radius:6px; font-size:12px; margin-bottom:12px;">
                {{ session('status') }}
            </div>
            @endif
            @if(session('error'))
            <div style="background:#fef2f2; border:1px solid #fca5a5; color:#b91c1c; padding:10px 12px; border-radius:6px; font-size:12px; margin-bottom:12px;">
                {{ session('error') }}
            </div>
            @endif
            @if(session('warning'))
            <div style="background:#fffbeb; border:1px solid #fde68a; color:#92400e; padding:10px 12px; border-radius:6px; font-size:12px; margin-bottom:12px;">
                {{ session('warning') }}
            </div>
            @endif

            @if($coupon)
                <div style="display: flex; justify-content: space-between; align-items: center; background: #f0fdf4; padding: 10px 14px; border: 1px dashed #10b981; border-radius: 6px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#047857" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 0 0-2 2v3a2 2 0 0 1 0 4v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a2 2 0 0 1 0-4V7a2 2 0 0 0-2-2H5z"/></svg>
                        <span style="font-size: 13px; font-weight: 600; color: #047857; text-transform: uppercase;">{{ $coupon->code }}</span>
                        <span style="font-size: 11px; color: #065f46;">(Đã áp dụng)</span>
                    </div>
                    <form action="{{ route('coupon.remove') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" style="background: none; border: none; cursor: pointer; color: #6b7280; font-size: 12px; font-weight: 600; text-decoration: underline;">Gỡ</button>
                    </form>
                </div>
            @else
                <form action="{{ route('coupon.apply') }}" method="POST" style="display: flex; gap: 10px; margin: 0;">
                    @csrf
                    <input type="text" name="code" placeholder="Mã giảm giá (Voucher)" required style="flex: 1; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 13px; text-transform: uppercase; font-family: inherit;">
                    <button type="submit" style="padding: 10px 16px; background: #4a5845; color: white; border: none; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; letter-spacing: 0.05em; text-transform: uppercase;">Áp Dụng</button>
                </form>
            @endif
        </div>

        <!-- Loyalty points Form -->
        @if(Auth::check())
        <div style="margin-top: 10px; margin-bottom: 20px; background: #fafaf9; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px 16px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 600; color: #374151; display: flex; align-items: center; gap: 6px; font-family: inherit;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4a5845" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    Điểm tích lũy: <span style="color: #4a5845; font-weight: 700;">{{ Auth::user()->loyalty_points }} điểm</span>
                </span>
                <span style="font-size: 11px; color: #6b7280;">(Quy đổi: {{ number_format(Auth::user()->loyalty_points * 100, 0, ',', '.') }}đ)</span>
            </div>
            @if(Auth::user()->loyalty_points > 0)
                <form action="{{ route('checkout.toggle-points') }}" method="POST" id="form-toggle-points" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                    @csrf
                    <input type="checkbox" name="use_points" value="1" id="use_points" onchange="document.getElementById('form-toggle-points').submit()" {{ session('checkout.use_points') ? 'checked' : '' }} style="width: 15px; height: 15px; accent-color: #4a5845; cursor: pointer;">
                    <label for="use_points" style="font-size: 12px; color: #4b5563; cursor: pointer; user-select: none; font-weight: 500;">Sử dụng điểm tích lũy thanh toán</label>
                </form>
            @else
                <p style="font-size: 11px; color: #9ca3af; margin: 0;">Bạn chưa có điểm tích lũy để sử dụng.</p>
            @endif
        </div>
        @endif

        <!-- Divider -->
        <div style="border-top: 1px solid #e5e7eb; margin-bottom: 20px;"></div>

        <!-- Order Summary -->
        <div style="display: flex; flex-direction: column; gap: 14px;">
            <div style="display: flex; justify-content: space-between;">
                <span style="font-size: 13px; color: #6b7280;">Tạm Tính ({{ $cartItems->count() }})</span>
                <span style="font-size: 13px; color: #374151;">{{ number_format($cartTotal, 0, ',', '.') }}đ</span>
            </div>
            @if($discountAmount > 0)
            <div style="display: flex; justify-content: space-between; color: #047857;">
                <span style="font-size: 13px;">Giảm giá (Voucher)</span>
                <span style="font-size: 13px; font-weight: 600;">-{{ number_format($discountAmount, 0, ',', '.') }}đ</span>
            </div>
            @endif
            @if($tierDiscount > 0)
            <div style="display: flex; justify-content: space-between; color: #047857;">
                <span style="font-size: 13px;">Hạng thành viên ({{ strtoupper($user->member_tier) }} -{{ $tierPercent }}%)</span>
                <span style="font-size: 13px; font-weight: 600;">-{{ number_format($tierDiscount, 0, ',', '.') }}đ</span>
            </div>
            @endif
            @if($pointsDiscount > 0)
            <div style="display: flex; justify-content: space-between; color: #047857;">
                <span style="font-size: 13px;">Đã dùng điểm ({{ $pointsRedeemed }} điểm)</span>
                <span style="font-size: 13px; font-weight: 600;">-{{ number_format($pointsDiscount, 0, ',', '.') }}đ</span>
            </div>
            @endif
            <div style="display: flex; justify-content: space-between;">
                <span style="font-size: 13px; color: #6b7280;">Thuế (8%)</span>
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
             <p style="font-size: 10px; color: #9ca3af; line-height: 1.6; margin: 0; margin-top: 8px;">
                Tổng số tiền bạn thanh toán đã bao gồm toàn bộ thuế và phí hải quan áp dụng. Chúng tôi cam kết không thu thêm bất kỳ khoản phí nào khi giao hàng.
            </p>
        </div>
    </div>

</div>

</body>
</html>
