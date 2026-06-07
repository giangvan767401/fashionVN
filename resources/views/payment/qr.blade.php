<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Thanh toán chuyển khoản') }} – Lumiere</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col justify-between">

    <!-- Header / Navbar -->
    <header class="bg-white border-b border-gray-200 py-4 px-6 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="{{ route('home') }}" class="flex flex-col">
                <span class="font-light text-xl tracking-wider text-gray-800 uppercase">Lumiere<span class="text-green-600 text-xs ml-0.5">●</span></span>
                <span class="text-[8px] tracking-[0.25em] text-gray-400 uppercase -mt-1">women clothing</span>
            </a>
            <div class="flex items-center gap-4">
                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full font-medium">VietQR / SePay Payment</span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-5xl w-full mx-auto px-4 py-8 md:py-12">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
            
            <!-- Left Panel: Order Details -->
            <div class="md:col-span-5 bg-white border border-gray-200 rounded-2xl p-6 md:p-8 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-800 mb-6 border-b border-gray-100 pb-4">{{ __('Thông tin đơn hàng') }}</h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-start">
                        <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">{{ __('Mã Đơn Hàng') }}</span>
                        <span class="text-sm font-bold text-gray-800">{{ $order->order_number }}</span>
                    </div>

                    <div class="flex justify-between items-start">
                        <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">{{ __('Khách Hàng') }}</span>
                        <span class="text-sm font-medium text-gray-700">{{ $order->ship_name }}</span>
                    </div>

                    <div class="flex justify-between items-start">
                        <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">{{ __('Số Điện Thoại') }}</span>
                        <span class="text-sm font-medium text-gray-700">{{ $order->ship_phone }}</span>
                    </div>

                    <div class="border-t border-gray-100 pt-4 flex justify-between items-baseline">
                        <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">{{ __('Tổng Tiền') }}</span>
                        <span class="text-2xl font-extrabold text-green-700">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
                    </div>
                </div>

                <!-- Timer / Expiry -->
                <div class="mt-8 bg-green-50 border border-green-100 rounded-xl p-4 flex flex-col items-center">
                    <span class="text-xs text-green-800 font-semibold mb-2">{{ __('Giao dịch hết hạn sau') }}</span>
                    <div class="flex gap-2 text-white">
                        <div class="flex flex-col items-center">
                            <span id="timer-min" class="bg-green-700 font-bold px-3 py-1.5 rounded-lg text-lg min-w-[36px] text-center">15</span>
                            <span class="text-[8px] text-green-600 font-bold uppercase mt-1">phút</span>
                        </div>
                        <span class="text-green-700 font-bold text-lg self-center -mt-4">:</span>
                        <div class="flex flex-col items-center">
                            <span id="timer-sec" class="bg-green-700 font-bold px-3 py-1.5 rounded-lg text-lg min-w-[36px] text-center">00</span>
                            <span class="text-[8px] text-green-600 font-bold uppercase mt-1">giây</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <a href="{{ route('profile.orders') }}" class="text-xs text-gray-400 hover:text-gray-600 underline transition-colors">
                        {{ __('Quản lý đơn hàng của tôi') }}
                    </a>
                </div>
            </div>

            <!-- Right Panel: QR Code & Transfer Info -->
            <div class="md:col-span-7 bg-white border border-gray-200 rounded-2xl p-6 md:p-8 shadow-sm flex flex-col items-center">
                <h2 class="text-lg font-semibold text-gray-800 mb-2 text-center">{{ __('Quét mã QR để thanh toán') }}</h2>
                <p class="text-xs text-gray-400 text-center mb-8 px-4">{{ __('Mở ứng dụng ngân hàng bất kỳ của bạn, quét mã bên dưới để tự động điền thông tin.') }}</p>

                <!-- QR Container -->
                <div class="relative bg-white p-6 border border-gray-100 rounded-2xl shadow-md max-w-[280px] w-full mb-8 flex justify-center items-center">
                    <img src="{{ $qrUrl }}" alt="QR thanh toán" class="w-[220px] h-[220px] object-contain">
                    
                    <!-- QR Frame corners -->
                    <div class="absolute top-3 left-3 w-6 h-6 border-t-4 border-l-4 border-green-600 rounded-tl-md"></div>
                    <div class="absolute top-3 right-3 w-6 h-6 border-t-4 border-r-4 border-green-600 rounded-tr-md"></div>
                    <div class="absolute bottom-3 left-3 w-6 h-6 border-b-4 border-l-4 border-green-600 rounded-bl-md"></div>
                    <div class="absolute bottom-3 right-3 w-6 h-6 border-b-4 border-r-4 border-green-600 rounded-br-md"></div>
                </div>

                <!-- Transfer Info -->
                <div class="w-full bg-gray-50 border border-gray-200 rounded-xl p-5 space-y-3.5 mb-6 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 font-medium">{{ __('Ngân hàng') }}</span>
                        <span class="font-bold text-gray-800">{{ env('BANK_NAME') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 font-medium">{{ __('Số tài khoản') }}</span>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-800" id="tk-num">{{ env('BANK_ACCOUNT_NUMBER') }}</span>
                            <button onclick="copyText('tk-num', this)" class="text-xs bg-white hover:bg-gray-100 border border-gray-300 rounded px-2 py-0.5 text-gray-600 transition-colors font-medium">Copy</button>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 font-medium">{{ __('Chủ tài khoản') }}</span>
                        <span class="font-bold text-gray-800 uppercase">{{ env('BANK_ACCOUNT_NAME') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 font-medium">{{ __('Số tiền') }}</span>
                        <span class="font-bold text-green-700 font-semibold">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 font-medium">{{ __('Nội dung') }}</span>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-green-800 bg-green-50 px-2.5 py-1 border border-green-200 rounded" id="ck-content">{{ $content }}</span>
                            <button onclick="copyText('ck-content', this)" class="text-xs bg-white hover:bg-gray-100 border border-gray-300 rounded px-2 py-1 text-gray-600 transition-colors font-medium">Copy</button>
                        </div>
                    </div>
                </div>

                <!-- Status indicator -->
                <div class="w-full flex items-center justify-center gap-3 py-4 border border-dashed border-gray-300 rounded-xl bg-gray-50/50" id="statusBox">
                    <svg class="animate-spin h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm text-gray-600 font-medium">{{ __('Đang chờ quý khách chuyển khoản...') }}</span>
                </div>

                <p class="text-[11px] text-gray-400 mt-4 text-center">⚡ {{ __('Hệ thống tự động xác nhận trong vòng 10-15 giây sau khi có tiền về tài khoản.') }}</p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-400">
        <div class="max-w-7xl mx-auto px-4">
            &copy; {{ date('Y') }} Lumiere Store. All rights reserved. Powered by SePay.
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Copy text helper
        function copyText(elementId, button) {
            const text = document.getElementById(elementId).innerText.trim();
            navigator.clipboard.writeText(text).then(() => {
                const originalText = button.innerText;
                button.innerText = 'Copied!';
                button.classList.add('bg-green-600', 'text-white', 'border-green-600');
                button.classList.remove('bg-white', 'text-gray-600');
                
                setTimeout(() => {
                    button.innerText = originalText;
                    button.classList.remove('bg-green-600', 'text-white', 'border-green-600');
                    button.classList.add('bg-white', 'text-gray-600');
                }, 2000);
            });
        }

        // Timer Countdown
        let remainingSeconds = 15 * 60; // 15 minutes
        const timerInterval = setInterval(() => {
            if (remainingSeconds <= 0) {
                clearInterval(timerInterval);
                document.getElementById('statusBox').innerHTML = 
                    `<span class="text-red-500 font-bold">⚠️ Giao dịch đã hết hạn. Vui lòng tạo đơn hàng mới!</span>`;
                return;
            }
            remainingSeconds--;
            const mins = Math.floor(remainingSeconds / 60);
            const secs = remainingSeconds % 60;
            document.getElementById('timer-min').innerText = String(mins).padStart(2, '0');
            document.getElementById('timer-sec').innerText = String(secs).padStart(2, '0');
        }, 1000);

        // Polling Order Status
        const orderId = {{ $order->id }};
        const checkStatus = setInterval(async () => {
            try {
                const res = await fetch(`/api/orders/${orderId}/status`);
                const data = await res.json();

                if (data.status === 'processing' || data.status === 'confirmed' || data.status === 'finished') {
                    clearInterval(checkStatus);
                    clearInterval(timerInterval);
                    
                    // Display success status
                    document.getElementById('statusBox').innerHTML = `
                        <div class="flex items-center gap-3 text-green-700 font-bold">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>✅ Thanh toán thành công! Đang chuyển hướng...</span>
                        </div>
                    `;
                    
                    // Redirect to order success/detail page
                    setTimeout(() => {
                        window.location.href = `/checkout/success/${orderId}`;
                    }, 2000);
                }
            } catch (error) {
                console.error('Lỗi khi kiểm tra trạng thái đơn hàng:', error);
            }
        }, 3000); // Poll every 3 seconds
    </script>
</body>
</html>
