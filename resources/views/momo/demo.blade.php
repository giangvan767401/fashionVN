<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cổng thanh toán MoMo</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
        }

        /* Top bar */
        .topbar {
            background: #fff;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 24px;
            border-bottom: 1px solid #eee;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        }

        .topbar img {
            width: 40px;
            height: 40px;
            border-radius: 8px;
        }

        .topbar-title {
            font-size: 16px;
            font-weight: 700;
            color: #333;
        }

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
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            padding: 28px 24px;
            width: 280px;
            flex-shrink: 0;
        }

        .order-card h3 {
            font-size: 15px;
            font-weight: 800;
            color: #222;
            margin-bottom: 20px;
        }

        .order-row {
            margin-bottom: 16px;
        }

        .order-row label {
            display: block;
            font-size: 11px;
            color: #aaa;
            font-weight: 600;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .order-row .val {
            font-size: 14px;
            color: #222;
            font-weight: 700;
        }

        .order-row .amount {
            font-size: 26px;
            font-weight: 800;
            color: #222;
        }

        .demo-badge {
            display: inline-block;
            background: #fff3cd;
            color: #856404;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 20px;
            border: 1px solid #ffc10740;
            margin-bottom: 16px;
            letter-spacing: 0.05em;
        }

        /* Right: payment UI */
        .payment-card {
            flex: 1;
            background: linear-gradient(160deg, #c2185b 0%, #ad1457 40%, #880e4f 100%);
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(173, 20, 87, 0.3);
            padding: 32px 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #fff;
        }

        .momo-logo-big {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .momo-logo-big img {
            width: 48px;
            height: 48px;
            object-fit: contain;
        }

        .payment-title {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .payment-sub {
            font-size: 13px;
            opacity: 0.85;
            margin-bottom: 28px;
        }

        /* Fake QR */
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
            width: 180px;
            height: 180px;
            background: #fff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .qr-note {
            font-size: 12px;
            color: #888;
            font-weight: 600;
            text-align: center;
        }

        /* Confirm button */
        .btn-confirm {
            width: 100%;
            padding: 16px;
            background: #fff;
            color: #ad1457;
            font-size: 16px;
            font-weight: 800;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            letter-spacing: 0.02em;
        }

        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        .btn-confirm:active {
            transform: scale(0.98);
        }

        .demo-note {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 14px;
            text-align: center;
        }

        .back-link {
            color: #A50064;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 16px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 640px) {
            .container {
                flex-direction: column;
            }

            .order-card {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="topbar">
        <img src="{{ asset('user/img/momo_circle.svg') }}" alt="MoMo">
        <span class="topbar-title">Cổng thanh toán MoMo</span>
    </div>

    <div class="container">

        <!-- Left: order info -->
        <div class="order-card">
            <span class="demo-badge">⚡ Lumiere cảm ơn!!</span>
            <h3>Thông tin đơn hàng</h3>

            <div class="order-row">
                <label>Nhà cung cấp</label>
                <div style="display:flex;align-items:center;gap:8px;">
                    <img src="{{ asset('user/img/momo_circle.svg') }}" width="24" height="24" style="border-radius:50%;">
                    <span class="val">Lumiere</span>
                </div>
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
                <span class="amount">{{ number_format($amount, 0, ',', '.') }}đ</span>
            </div>

            <!-- Countdown -->
            <div style="background:#fdf0f5; border-radius:12px; padding:14px; margin-top:18px; text-align:center;">
                <div style="font-size:12px; color:#ae2070; font-weight:700; margin-bottom:10px;">Đơn hàng sẽ hết hạn sau:</div>
                <div style="display:flex; justify-content:center; gap:10px;">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:3px;">
                        <div id="cd-h" style="background:#ae2070;color:#fff;font-size:18px;font-weight:800;width:44px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;">16</div>
                        <span style="font-size:9px;color:#ae2070;font-weight:700;text-transform:uppercase;">Giờ</span>
                    </div>
                    <div style="display:flex;flex-direction:column;align-items:center;gap:3px;">
                        <div id="cd-m" style="background:#ae2070;color:#fff;font-size:18px;font-weight:800;width:44px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;">39</div>
                        <span style="font-size:9px;color:#ae2070;font-weight:700;text-transform:uppercase;">Phút</span>
                    </div>
                    <div style="display:flex;flex-direction:column;align-items:center;gap:3px;">
                        <div id="cd-s" style="background:#ae2070;color:#fff;font-size:18px;font-weight:800;width:44px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;">00</div>
                        <span style="font-size:9px;color:#ae2070;font-weight:700;text-transform:uppercase;">Giây</span>
                    </div>
                </div>
            </div>

            <!-- Nút Quay về -->
            <form method="POST" action="{{ route('momo.demo.cancel') }}" style="margin-top:16px; text-align:center;">
                @csrf
                <input type="hidden" name="order_number" value="{{ $order->order_number }}">
                <button type="submit" style="background:none; border:none; color:#ae2070; font-size:14px; font-weight:700; cursor:pointer; text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'" onclick="return confirm('Bạn có chắc chắn muốn huỷ giao dịch?')">
                    Quay về
                </button>
            </form>
        </div>

        <!-- Right: payment -->
        <div class="payment-card">
            <div class="momo-logo-big">
                <img src="{{ asset('user/img/momo_circle.svg') }}" alt="MoMo">
            </div>
            <div class="payment-title">Thanh toán MoMo</div>
            <div class="payment-sub">Quét mã QR bằng App MoMo để thanh toán</div>

            <div class="qr-box">
                <div class="qr-img">
                    <img src="{{ asset('user/img/momo_qr.svg') }}" alt="QR Code" style="width:160px;height:160px;object-fit:contain;border-radius:8px;">
                </div>
                <div class="qr-note">Sử dụng App MoMo hoặc ứng dụng ngân hàng để quét mã</div>
            </div>

            <form method="POST" action="{{ route('momo.demo.confirm') }}" style="width:100%;">
                @csrf
                <input type="hidden" name="order_number" value="{{ $order->order_number }}">
                <button type="submit" class="btn-confirm">
                    ✓ &nbsp; Xác nhận thanh toán thành công
                </button>
            </form>

            <p class="demo-note">* Gặp khó khăn khi thanh toán?</p>
        </div>

    </div>

</body>

<script>
    let total = 16 * 3600 + 39 * 60;

    function tick() {
        if (total <= 0) return;
        total--;
        document.getElementById('cd-h').textContent = String(Math.floor(total / 3600)).padStart(2, '0');
        document.getElementById('cd-m').textContent = String(Math.floor((total % 3600) / 60)).padStart(2, '0');
        document.getElementById('cd-s').textContent = String(total % 60).padStart(2, '0');
    }
    setInterval(tick, 1000);
</script>

</html>