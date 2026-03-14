<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Hàng Thành Công – Lumiere</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: #fdfdfc;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-6">

    <div class="max-w-xl w-full bg-white p-10 md:p-14 text-center" style="border: 1px solid #eaeaea; box-shadow: 0 4px 24px rgba(0,0,0,0.02);">

        <div class="mx-auto w-16 h-16 rounded-full flex items-center justify-center mb-6" style="background: #eef2eb;">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#6b8c5e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>

        <h1 class="text-2xl font-semibold text-gray-900 mb-2">Đơn Hàng Của Bạn Đã Được Đặt!</h1>
        <p class="text-gray-500 text-sm mb-8">Cảm ơn bạn đã lựa chọn Lumiere. Chúng tôi đã gửi email xác nhận cùng thông tin chi tiết của đơn hàng.</p>

        @if(session('success'))
        <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded text-sm text-center">
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-gray-50 p-6 rounded text-left mb-8 border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <span class="text-sm font-medium text-gray-500">Mã Đơn Hàng</span>
                <span class="text-base font-semibold text-gray-900">{{ $order->order_number }}</span>
            </div>
            <div class="flex justify-between items-center mb-4">
                <span class="text-sm font-medium text-gray-500">Tổng Tiền</span>
                <span class="text-base font-semibold text-gray-900">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
            </div>
            <div class="flex justify-between items-center mb-4">
                <span class="text-sm font-medium text-gray-500">Thanh Toán</span>
                @if($order->payment_status === 'paid')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    Đã thanh toán
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                    Thanh toán khi nhận hàng
                </span>
                @endif
            </div>
            <div class="pt-4 border-t border-gray-200">
                <span class="block text-sm font-medium text-gray-500 mb-2">Giao Đến</span>
                <span class="block text-sm text-gray-800">{{ $order->ship_name }}</span>
                <span class="block text-sm text-gray-800">{{ $order->ship_address }}</span>
                <span class="block text-sm text-gray-800">{{ $order->ship_province }}</span>
            </div>
        </div>

        <a href="{{ route('home') }}" class="inline-block px-10 py-3 text-sm font-medium text-white rounded transition-colors" style="background: #4a5845; letter-spacing: 0.04em;">
            Tiếp Tục Mua Sắm
        </a>
    </div>

</body>

</html>