@php
    $user = Auth::user();
    $tier = $user->member_tier ?? 'bronze';
    $points = $user->loyalty_points ?? 0;
    $spent = (float)($user->total_spent ?? 0);

    $tierNames = [
        'bronze' => 'Đồng (Bronze)',
        'silver' => 'Bạc (Silver)',
        'gold' => 'Vàng (Gold)',
        'diamond' => 'Kim Cương (Diamond)'
    ];
    $tierName = $tierNames[$tier] ?? 'Đồng (Bronze)';

    // Brand gradient matching the aesthetic and tier
    $tierGradients = [
        'bronze' => 'linear-gradient(135deg, #7A533A 0%, #B88E74 100%)',
        'silver' => 'linear-gradient(135deg, #4A5568 0%, #A0AEC0 100%)',
        'gold' => 'linear-gradient(135deg, #9C7A28 0%, #E5C25F 50%, #B08D33 100%)',
        'diamond' => 'linear-gradient(135deg, #111827 0%, #1F2937 50%, #374151 100%)'
    ];
    $cardGradient = $tierGradients[$tier] ?? $tierGradients['bronze'];

    // Next tier progress logic
    $nextTierName = '';
    $nextTarget = 0;
    if ($spent < 2000000) {
        $nextTierName = 'Bạc (Silver)';
        $nextTarget = 2000000;
    } elseif ($spent < 5000000) {
        $nextTierName = 'Vàng (Gold)';
        $nextTarget = 5000000;
    } elseif ($spent < 15000000) {
        $nextTierName = 'Kim Cương (Diamond)';
        $nextTarget = 15000000;
    }

    $progressPercent = 0;
    $needed = 0;
    if ($nextTarget > 0) {
        $progressPercent = min(100, ($spent / $nextTarget) * 100);
        $needed = $nextTarget - $spent;
    } else {
        $progressPercent = 100;
    }
@endphp

<!-- LOYALTY CARD CONTAINER -->
<div class="mb-6 font-[Montserrat]">
    <!-- VIP Card -->
    <div style="background: {{ $cardGradient }}; position: relative; overflow: hidden; border-radius: 16px; padding: 1.5rem; color: #ffffff; box-shadow: 0 10px 20px rgba(0,0,0,0.12), 0 4px 6px rgba(0,0,0,0.06); transition: transform 0.3s ease;">
        <!-- Card gloss overlay -->
        <div style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; background: linear-gradient(220deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 60%); pointer-events: none;"></div>
        <!-- Card circular decorative glow -->
        <div style="position: absolute; right: -20px; bottom: -20px; width: 120px; height: 120px; border-radius: 50%; background: rgba(255,255,255,0.05); pointer-events: none;"></div>
        
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <span style="font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: rgba(255,255,255,0.9);">
                LUMIERE CLUB
            </span>
            <!-- Contactless chip simulation -->
            <div style="width: 38px; height: 26px; border-radius: 6px; background: linear-gradient(135deg, #f3e5ab, #d4af37); opacity: 0.85; position: relative; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,0.2);">
                <div style="width: 20px; height: 14px; border: 1px solid rgba(0,0,0,0.15); border-radius: 2px;"></div>
            </div>
        </div>

        <!-- Middle Info -->
        <div style="margin-bottom: 1.5rem;">
            <p style="font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.65); margin: 0 0 2px 0;">
                Hạng thành viên
            </p>
            <h3 style="font-size: 20px; font-weight: 700; letter-spacing: 1px; margin: 0; text-transform: uppercase; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                {{ $tierName }}
            </h3>
        </div>

        <!-- Footer Info -->
        <div style="display: flex; justify-content: space-between; align-items: flex-end; border-top: 1px solid rgba(255,255,255,0.15); padding-top: 0.75rem;">
            <div>
                <p style="font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.65); margin: 0 0 2px 0;">
                    Điểm tích lũy
                </p>
                <p style="font-size: 16px; font-weight: 700; margin: 0;">
                    {{ number_format($points) }} <span style="font-size: 12px; font-weight: 400; opacity: 0.85;">điểm</span>
                </p>
            </div>
            <div style="text-align: right;">
                <p style="font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.65); margin: 0 0 2px 0;">
                    Tổng chi tiêu
                </p>
                <p style="font-size: 14px; font-weight: 700; margin: 0;">
                    {{ number_format($spent, 0, ',', '.') }}đ
                </p>
            </div>
        </div>
    </div>

    <!-- Tier Progress Details -->
    <div class="mt-4 bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
        @if($nextTarget > 0)
            <div class="flex justify-between items-center text-[12px] font-medium text-gray-700 mb-2">
                <span>Nâng hạng tiếp theo: <strong>{{ $nextTierName }}</strong></span>
                <span class="text-[#61715B]">{{ round($progressPercent) }}%</span>
            </div>
            <!-- Progress Bar -->
            <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden mb-2">
                <div class="h-full bg-[#61715B] rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
            </div>
            <p class="text-[11px] text-gray-500 text-right">
                Cần chi tiêu thêm: <strong class="text-gray-900">{{ number_format($needed, 0, ',', '.') }}đ</strong>
            </p>
        @else
            <div class="flex items-center gap-2 p-2 bg-emerald-50 text-emerald-800 rounded-lg text-xs font-semibold border border-emerald-100">
                <span>🎉</span>
                <span>Chúc mừng! Bạn đã đạt hạng thành viên tối đa (Kim Cương).</span>
            </div>
        @endif
    </div>

    <!-- Active perks list -->
    <div class="mt-3 bg-gray-50 rounded-xl p-4 border border-gray-100 text-[12px] text-gray-600">
        <h4 class="font-bold text-gray-900 mb-2.5 flex items-center gap-1.5">
            <!-- Sparkles/Star Icon -->
            <svg class="w-4 h-4 text-[#61715B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
            </svg>
            Ưu đãi Hạng {{ $user->member_tier === 'diamond' ? 'Kim Cương' : ($user->member_tier === 'gold' ? 'Vàng' : ($user->member_tier === 'silver' ? 'Bạc' : 'Đồng')) }}
        </h4>
        <ul class="space-y-1.5 list-none pl-0 font-light">
            <li class="flex items-start gap-1.5">
                <span class="text-[#61715B] font-bold">✓</span>
                @if($tier === 'bronze')
                    <span>Giảm trực tiếp <strong>0%</strong> hóa đơn.</span>
                @elseif($tier === 'silver')
                    <span>Giảm trực tiếp <strong class="text-gray-900">2%</strong> trên tổng tiền hàng.</span>
                @elseif($tier === 'gold')
                    <span>Giảm trực tiếp <strong class="text-gray-900">5%</strong> trên tổng tiền hàng.</span>
                @elseif($tier === 'diamond')
                    <span>Giảm trực tiếp <strong class="text-gray-900">10%</strong> trên tổng tiền hàng.</span>
                @endif
            </li>
            <li class="flex items-start gap-1.5">
                <span class="text-[#61715B] font-bold">✓</span>
                @if($tier === 'diamond')
                    <span>Tích lũy điểm thưởng nhân đôi: <strong>x2 điểm</strong> (2 điểm mỗi 10.000đ).</span>
                @else
                    <span>Tích lũy <strong>1 điểm</strong> mỗi 10.000đ chi tiêu.</span>
                @endif
            </li>
            <li class="flex items-start gap-1.5">
                <span class="text-[#61715B] font-bold">✓</span>
                <span>Thanh toán bằng điểm tích lũy: <strong>1 điểm = 100đ</strong>.</span>
            </li>
        </ul>
    </div>
</div>
