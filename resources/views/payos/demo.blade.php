<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cổng thanh toán VietQR</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background: #f5f5f7;
            min-height: 100vh;
        }

        /* Top bar */
        .topbar {
            background: #fff;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 24px;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .topbar-title {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.05em;
        }

        .topbar-logo {
            font-size: 20px;
            font-weight: 800;
            color: #2563eb;
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 28px 24px;
            width: 280px;
            flex-shrink: 0;
            border: 1px solid #e2e8f0;
        }

        .order-card h3 {
            font-size: 15px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 20px;
        }

        .order-row {
            margin-bottom: 16px;
        }

        .order-row label {
            display: block;
            font-size: 11px;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .order-row .val {
            font-size: 14px;
            color: #1e293b;
            font-weight: 700;
        }

        .order-row .amount {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
        }

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
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.2);
            padding: 32px 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .payment-title {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .payment-sub {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 28px;
        }

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
            border: 1px solid rgba(255, 255, 255, 0.1);
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
            color: #64748b;
            font-weight: 600;
            text-align: center;
        }

        /* Confirm button */
        .btn-confirm {
            width: 100%;
            padding: 16px;
            background: #fff;
            color: #2563eb;
            font-size: 15px;
            font-weight: 800;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            letter-spacing: 0.02em;
        }

        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
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
        <span class="topbar-logo">Viet<span style="color:#0f172a;">QR</span></span>
        <span class="topbar-title">| CỔNG THANH TOÁN LIÊN NGÂN HÀNG</span>
    </div>

    <div class="container">

        <!-- Left: order info -->
        <div class="order-card">
            <span class="demo-badge">⚡ Lumiere Store</span>
            <h3>Thông tin đơn hàng</h3>

            <div class="order-row">
                <label>Nhà cung cấp</label>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span class="val" style="color: #2563eb;">Lumiere Clothes</span>
                </div>
            </div>

            <div class="order-row">
                <label>Mã đơn hàng</label>
                <span class="val">{{ $order->order_number }}</span>
            </div>

            <div class="order-row">
                <label>Mô tả</label>
                <span class="val">Chuyển khoản đơn hàng Lumiere</span>
            </div>

            <div class="order-row">
                <label>Số tiền</label>
                <span class="amount">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
            </div>

            <!-- Countdown -->
            <div style="background:#eff6ff; border-radius:12px; padding:14px; margin-top:18px; text-align:center; border: 1px solid #bfdbfe;">
                <div style="font-size:12px; color:#1e40af; font-weight:700; margin-bottom:10px;">Giao dịch hết hạn sau:</div>
                <div style="display:flex; justify-content:center; gap:10px;">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:3px;">
                        <div id="cd-m" style="background:#2563eb;color:#fff;font-size:18px;font-weight:800;width:44px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;">15</div>
                        <span style="font-size:9px;color:#1e40af;font-weight:700;text-transform:uppercase;">Phút</span>
                    </div>
                    <div style="display:flex;flex-direction:column;align-items:center;gap:3px;">
                        <div id="cd-s" style="background:#2563eb;color:#fff;font-size:18px;font-weight:800;width:44px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;">00</div>
                        <span style="font-size:9px;color:#1e40af;font-weight:700;text-transform:uppercase;">Giây</span>
                    </div>
                </div>
            </div>

            <!-- Back Link -->
            <form method="POST" action="{{ route('payos.cancel', $order->id) }}" style="margin-top:16px; text-align:center;" class="confirm-form" data-confirm-text="Bạn có chắc chắn muốn huỷ giao dịch?">
                @csrf
                <button type="submit" style="background:none; border:none; color:#2563eb; font-size:14px; font-weight:700; cursor:pointer; text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                    Quay về
                </button>
            </form>
        </div>

        <!-- Right: payment -->
        <div class="payment-card">
            <div class="payment-title">Chuyển khoản VietQR</div>
            <div class="payment-sub">Quét mã QR bằng ứng dụng Ngân hàng để thanh toán</div>

            <!-- QR Tabs Switch -->
            <div style="display: flex; background: rgba(255, 255, 255, 0.15); border-radius: 30px; padding: 4px; margin-bottom: 20px; width: 100%;">
                <button type="button" onclick="setQrMode('bank')" id="btn-tab-bank" style="flex: 1; border: none; padding: 8px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; cursor: pointer; transition: all 0.3s; background: #fff; color: #2563eb;">
                    🏦 App Ngân hàng (VietQR)
                </button>
                <button type="button" onclick="setQrMode('mock')" id="btn-tab-mock" style="flex: 1; border: none; padding: 8px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; cursor: pointer; transition: all 0.3s; background: transparent; color: #fff;">
                    📱 Giả lập di động
                </button>
            </div>

            <div class="qr-box">
                <!-- Real VietQR image -->
                <div class="qr-img" id="qr-img-bank">
                    <img src="{{ $vietQrUrl }}" alt="VietQR Code" style="width:160px;height:160px;object-fit:contain;border-radius:8px;">
                </div>
                <!-- Mock scan redirect QR code -->
                <div class="qr-img" id="qr-img-mock" style="display: none;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($confirmScanUrl) }}" alt="Mock Code" style="width:160px;height:160px;object-fit:contain;border-radius:8px;">
                </div>
                <div class="qr-note" id="status-note" style="line-height: 1.5;">
                    Quét mã bằng bất kỳ App Ngân hàng để thanh toán tự động điền số tiền.<br>
                    <span style="font-size: 11px; color: #2563eb; font-weight: bold;">(Số tiền thanh toán: {{ number_format($order->total_amount, 0, ',', '.') }}đ)</span>
                </div>
            </div>

            <form id="confirm-payment-form" method="GET" action="{{ route('payos.return', $order->id) }}" style="width:100%;">
                <input type="hidden" name="status" value="PAID">
                <input type="hidden" name="id" value="MOCK-{{ strtoupper(uniqid()) }}">
                <button type="submit" class="btn-confirm">
                    ✓ &nbsp; Xác nhận đã chuyển khoản thành công
                </button>
            </form>

            <p class="demo-note">* Màn hình này sẽ tự động chuyển hướng khi quét mã thành công.</p>
        </div>

    </div>

</body>

<script>
    let total = 15 * 60;

    function tick() {
        if (total <= 0) return;
        total--;
        document.getElementById('cd-m').textContent = String(Math.floor(total / 60)).padStart(2, '0');
        document.getElementById('cd-s').textContent = String(total % 60).padStart(2, '0');
    }
    setInterval(tick, 1000);

    // Xử lý đổi chế độ mã QR
    function setQrMode(mode) {
        const btnBank = document.getElementById('btn-tab-bank');
        const btnMock = document.getElementById('btn-tab-mock');
        const qrBank = document.getElementById('qr-img-bank');
        const qrMock = document.getElementById('qr-img-mock');
        const statusNote = document.getElementById('status-note');
        const paymentSub = document.querySelector('.payment-sub');

        if (mode === 'bank') {
            btnBank.style.background = '#fff';
            btnBank.style.color = '#2563eb';
            btnMock.style.background = 'transparent';
            btnMock.style.color = '#fff';
            qrBank.style.display = 'flex';
            qrMock.style.display = 'none';
            if (paymentSub) {
                paymentSub.textContent = 'Quét mã QR bằng ứng dụng Ngân hàng để thanh toán';
            }
            if (statusNote) {
                statusNote.innerHTML = `Quét mã bằng bất kỳ App Ngân hàng để thanh toán tự động điền số tiền.<br><span style="font-size: 11px; color: #2563eb; font-weight: bold;">(Số tiền thanh toán: {{ number_format($order->total_amount, 0, ',', '.') }}đ)</span>`;
            }
        } else {
            btnMock.style.background = '#fff';
            btnMock.style.color = '#2563eb';
            btnBank.style.background = 'transparent';
            btnBank.style.color = '#fff';
            qrBank.style.display = 'none';
            qrMock.style.display = 'flex';
            if (paymentSub) {
                paymentSub.textContent = 'Quét mã QR bằng Zalo/Camera để mở giao diện thanh toán';
            }
            if (statusNote) {
                statusNote.innerHTML = `Quét bằng Zalo hoặc Camera điện thoại để mở trang xác nhận.<br><span style="font-size: 11px; color: #2563eb; font-weight: bold;">(Lưu ý: Điện thoại và máy tính cần kết nối cùng mạng Wi-Fi)</span>`;
            }
        }
    }

    // Kiểm tra trạng thái thanh toán từ máy chủ cứ mỗi 2 giây
    const confirmForm = document.getElementById('confirm-payment-form');
    const statusNote = document.getElementById('status-note');

    const checkPaymentInterval = setInterval(() => {
        fetch("{{ route('momo.check_status', $order->order_number) }}")
            .then(response => response.json())
            .then(data => {
                if (data.payment_status === 'paid') {
                    clearInterval(checkPaymentInterval);
                    if (statusNote) {
                        statusNote.innerHTML = `<span style="color: #16a34a; font-weight: bold; font-size: 14px;">✓ Đã nhận được thanh toán từ điện thoại!<br>Đang chuyển hướng...</span>`;
                    }
                    setTimeout(() => {
                        if (confirmForm) {
                            confirmForm.submit();
                        }
                    }, 1000);
                }
            })
            .catch(err => console.error("Lỗi kiểm tra thanh toán:", err));
    }, 2000);
</script>

</html>
