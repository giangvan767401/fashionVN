<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cổng thanh toán VNPAY</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Nunito', sans-serif;
            background: #f0f4ff;
            min-height: 100vh;
        }

        /* Top bar */
        .topbar {
            background: #fff;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 24px;
            border-bottom: 1px solid #dbeafe;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        .topbar-logo { font-size: 22px; font-weight: 900; color: #005baa; letter-spacing: -0.5px; }
        .topbar-logo span { color: #e31837; }
        .topbar-title { font-size: 14px; font-weight: 700; color: #1e293b; opacity: 0.6; letter-spacing: 0.05em; }

        /* Layout */
        .container {
            display: flex;
            gap: 20px;
            max-width: 900px;
            margin: 36px auto;
            padding: 0 16px;
        }

        /* Left: order info */
        .order-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 91, 170, 0.08);
            padding: 28px 24px;
            width: 280px;
            flex-shrink: 0;
            border: 1px solid #dbeafe;
        }
        .order-card h3 { font-size: 15px; font-weight: 800; color: #1e293b; margin-bottom: 20px; }

        .order-row { margin-bottom: 16px; }
        .order-row label {
            display: block;
            font-size: 11px;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .order-row .val { font-size: 14px; color: #1e293b; font-weight: 700; }
        .order-row .amount { font-size: 24px; font-weight: 800; color: #0f172a; }

        .demo-badge {
            display: inline-block;
            background: #dbeafe;
            color: #1e40af;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 20px;
            margin-bottom: 16px;
            letter-spacing: 0.05em;
        }

        /* Right: payment UI */
        .payment-card {
            flex: 1;
            background: linear-gradient(135deg, #003d7a 0%, #005baa 50%, #1a7fd4 100%);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 91, 170, 0.3);
            padding: 32px 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .vnpay-logo-big {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }
        .vnpay-logo-big .vn { font-size: 36px; font-weight: 900; color: #fff; }
        .vnpay-logo-big .pay { font-size: 36px; font-weight: 900; color: #e31837; }

        .payment-title { font-size: 20px; font-weight: 800; margin-bottom: 6px; }
        .payment-sub { font-size: 13px; opacity: 0.85; margin-bottom: 24px; text-align: center; }

        /* QR Box */
        .qr-box {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            width: 100%;
        }
        .qr-img {
            width: 180px; height: 180px;
            background: #fff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .qr-note {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            text-align: center;
        }

        /* Confirm button */
        .btn-confirm {
            width: 100%;
            padding: 16px;
            background: #fff;
            color: #005baa;
            font-size: 15px;
            font-weight: 800;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            letter-spacing: 0.02em;
        }
        .btn-confirm:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18); }
        .btn-confirm:active { transform: scale(0.98); }

        .demo-note { font-size: 11px; opacity: 0.7; margin-top: 14px; text-align: center; }

        /* Countdown */
        .countdown-box {
            background: #e0f2fe;
            border-radius: 12px;
            padding: 14px;
            margin-top: 18px;
            text-align: center;
            border: 1px solid #bae6fd;
        }
        .countdown-title { font-size: 12px; color: #0369a1; font-weight: 700; margin-bottom: 10px; }
        .countdown-grid { display: flex; justify-content: center; gap: 10px; }
        .countdown-unit { display: flex; flex-direction: column; align-items: center; gap: 3px; }
        .countdown-digit {
            background: #005baa; color: #fff;
            font-size: 18px; font-weight: 800;
            width: 44px; height: 36px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }
        .countdown-label { font-size: 9px; color: #0369a1; font-weight: 700; text-transform: uppercase; }

        @media (max-width: 640px) {
            .container { flex-direction: column; }
            .order-card { width: 100%; }
        }
    </style>
</head>

<body>
    <div class="topbar">
        <div class="topbar-logo">VN<span>PAY</span></div>
        <span class="topbar-title">| CỔNG THANH TOÁN TRỰC TUYẾN</span>
    </div>

    <div class="container">

        <!-- Left: order info -->
        <div class="order-card">
            <span class="demo-badge">⚡ Lumiere Store</span>
            <h3>Thông tin đơn hàng</h3>

            <div class="order-row">
                <label>Nhà cung cấp</label>
                <span class="val" style="color:#005baa;">Lumiere Clothes</span>
            </div>
            <div class="order-row">
                <label>Mã đơn hàng</label>
                <span class="val">{{ $order->order_number }}</span>
            </div>
            <div class="order-row">
                <label>Mô tả</label>
                <span class="val">Thanh toán đơn hàng Lumiere</span>
            </div>
            <div class="order-row">
                <label>Số tiền</label>
                <span class="amount">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
            </div>

            <!-- Countdown -->
            <div class="countdown-box">
                <div class="countdown-title">Giao dịch hết hạn sau:</div>
                <div class="countdown-grid">
                    <div class="countdown-unit">
                        <div id="cd-m" class="countdown-digit">15</div>
                        <span class="countdown-label">Phút</span>
                    </div>
                    <div class="countdown-unit">
                        <div id="cd-s" class="countdown-digit">00</div>
                        <span class="countdown-label">Giây</span>
                    </div>
                </div>
            </div>

            <!-- Cancel -->
            <form method="POST" action="{{ route('vnpay.demo.cancel') }}" style="margin-top:16px; text-align:center;"
                class="confirm-form" data-confirm-text="Bạn có chắc chắn muốn huỷ giao dịch?">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <button type="submit"
                    style="background:none; border:none; color:#005baa; font-size:14px; font-weight:700; cursor:pointer;"
                    onmouseover="this.style.textDecoration='underline'"
                    onmouseout="this.style.textDecoration='none'">
                    Quay về
                </button>
            </form>
        </div>

        <!-- Right: payment -->
        <div class="payment-card">
            <div class="vnpay-logo-big">
                <span class="vn">VN</span><span class="pay">PAY</span>
            </div>
            <div class="payment-title">Thanh toán VNPAY</div>
            <div class="payment-sub" id="payment-sub-text">Quét mã QR bằng ứng dụng ngân hàng hoặc giả lập di động</div>

            <!-- QR Tab Switch -->
            <div style="display:flex; background:rgba(255,255,255,0.15); border-radius:30px; padding:4px; margin-bottom:20px; width:100%;">
                <button type="button" onclick="setQrMode('vnpay')" id="btn-tab-vnpay"
                    style="flex:1; border:none; padding:8px 12px; border-radius:20px; font-size:11px; font-weight:bold; cursor:pointer; transition:all 0.3s; background:#fff; color:#005baa;">
                    🔵 Cổng VNPAY (thật)
                </button>
                <button type="button" onclick="setQrMode('mock')" id="btn-tab-mock"
                    style="flex:1; border:none; padding:8px 12px; border-radius:20px; font-size:11px; font-weight:bold; cursor:pointer; transition:all 0.3s; background:transparent; color:#fff;">
                    📱 Giả lập di động
                </button>
            </div>

            <div class="qr-box">
                <!-- VNPAY Gateway QR (redirects to actual gateway) -->
                <div class="qr-img" id="qr-img-vnpay">
                    <div style="text-align:center; padding:20px;">
                        <div style="font-size:40px; margin-bottom:12px;">🏦</div>
                        <p style="font-size:12px; color:#475569; font-weight:700; line-height:1.5;">
                            Bấm nút bên dưới để<br>mở cổng thanh toán VNPAY
                        </p>
                    </div>
                </div>
                <!-- Mock scan QR -->
                <div class="qr-img" id="qr-img-mock" style="display:none;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($confirmScanUrl) }}"
                        alt="Mock QR" style="width:160px;height:160px;object-fit:contain;border-radius:8px;">
                </div>

                <div class="qr-note" id="status-note" style="line-height:1.5;">
                    Bấm <strong>"Xác nhận đã thanh toán"</strong> sau khi hoàn tất.<br>
                    <span style="font-size:11px; color:#005baa; font-weight:bold;">
                        (Số tiền: {{ number_format($order->total_amount, 0, ',', '.') }}đ)
                    </span>
                </div>
            </div>

            <!-- Confirm button (demo) -->
            <form id="confirm-payment-form" method="POST" action="{{ route('vnpay.demo.confirm') }}" style="width:100%;">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <button type="submit" class="btn-confirm">
                    ✓ &nbsp; Xác nhận đã thanh toán thành công
                </button>
            </form>

            <p class="demo-note">* Màn hình này sẽ tự động chuyển hướng khi quét mã thành công.</p>
        </div>

    </div>

</body>

<script>
    // Countdown
    let total = 15 * 60;
    function tick() {
        if (total <= 0) return;
        total--;
        document.getElementById('cd-m').textContent = String(Math.floor(total / 60)).padStart(2, '0');
        document.getElementById('cd-s').textContent = String(total % 60).padStart(2, '0');
    }
    setInterval(tick, 1000);

    // Tab switching
    function setQrMode(mode) {
        const btnVnpay = document.getElementById('btn-tab-vnpay');
        const btnMock  = document.getElementById('btn-tab-mock');
        const qrVnpay  = document.getElementById('qr-img-vnpay');
        const qrMock   = document.getElementById('qr-img-mock');
        const subText  = document.getElementById('payment-sub-text');
        const note     = document.getElementById('status-note');

        if (mode === 'vnpay') {
            btnVnpay.style.background = '#fff';
            btnVnpay.style.color = '#005baa';
            btnMock.style.background  = 'transparent';
            btnMock.style.color  = '#fff';
            qrVnpay.style.display = 'flex';
            qrMock.style.display  = 'none';
            subText.textContent = 'Bấm nút xác nhận để hoàn tất thanh toán VNPAY';
            note.innerHTML = `Bấm <strong>"Xác nhận đã thanh toán"</strong> sau khi hoàn tất.<br><span style="font-size:11px;color:#005baa;font-weight:bold;">(Số tiền: {{ number_format($order->total_amount, 0, ',', '.') }}đ)</span>`;
        } else {
            btnMock.style.background  = '#fff';
            btnMock.style.color  = '#005baa';
            btnVnpay.style.background = 'transparent';
            btnVnpay.style.color = '#fff';
            qrMock.style.display  = 'flex';
            qrVnpay.style.display = 'none';
            subText.textContent = 'Quét QR bằng Zalo/Camera để mở trang xác nhận trên điện thoại';
            note.innerHTML = `Quét bằng Zalo hoặc Camera điện thoại.<br><span style="font-size:11px;color:#005baa;font-weight:bold;">(Điện thoại & máy tính cần cùng mạng Wi-Fi)</span>`;
        }
    }

    // Polling check payment status every 3s
    const checkInterval = setInterval(() => {
        fetch("{{ route('momo.check_status', $order->order_number) }}")
            .then(r => r.json())
            .then(data => {
                if (data.payment_status === 'paid') {
                    clearInterval(checkInterval);
                    const note = document.getElementById('status-note');
                    if (note) {
                        note.innerHTML = `<span style="color:#16a34a;font-weight:bold;font-size:14px;">✓ Đã nhận được thanh toán từ điện thoại!<br>Đang chuyển hướng...</span>`;
                    }
                    setTimeout(() => {
                        document.getElementById('confirm-payment-form').submit();
                    }, 1200);
                }
            })
            .catch(err => console.error('Lỗi kiểm tra:', err));
    }, 3000);

    // SweetAlert confirm for cancel form
    document.querySelectorAll('.confirm-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const msg = this.dataset.confirmText || 'Bạn có chắc chắn?';
            Swal.fire({
                title: 'Xác nhận',
                text: msg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#005baa',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Không'
            }).then(result => {
                if (result.isConfirmed) form.submit();
            });
        });
    });
</script>

</html>
