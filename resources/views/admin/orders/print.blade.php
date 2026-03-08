<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In Đơn Hàng #{{ $order->order_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px; color: #333; line-height: 1.5; padding: 20px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #333; font-size: 28px; text-transform: uppercase; }
        .company-details { text-align: right; }
        .company-details strong { font-size: 18px; display: block; margin-bottom: 5px; }
        .order-info { margin-bottom: 30px; display: flex; justify-content: space-between; }
        .customer-info { width: 48%; }
        .order-details { width: 48%; text-align: right; }
        .order-details p, .customer-info p { margin: 5px 0; }
        table { width: 100%; text-align: left; border-collapse: collapse; margin-top: 20px; margin-bottom: 30px;}
        table th, table td { padding: 12px; border-bottom: 1px solid #ddd; }
        table th { background-color: #f8f8f8; font-weight: bold; text-transform: uppercase; font-size: 12px; }
        table td.right, table th.right { text-align: right; }
        table td.center, table th.center { text-align: center; }
        .total-row td { border-top: 2px solid #333; font-weight: bold; font-size: 16px; }
        .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #777; border-top: 1px solid #eee; padding-top: 20px;}
        @media print {
            body { padding: 0; }
            .invoice-box { box-shadow: none; border: none; max-width: 100%; padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="invoice-box">
        <div class="header">
            <div>
                <h1>HÓA ĐƠN</h1>
                <p>Mã đơn: <strong>{{ $order->order_number }}</strong></p>
                <p>Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="company-details">
                <strong>LUMIERE WOMEN CLOTHING</strong>
                <p>Hotline: 0987.654.321<br>
                Website: www.lumiere.com<br>
                Địa chỉ: 123 Đường Fashion, Quận 1, TP.HCM</p>
            </div>
        </div>

        <div class="order-info">
            <div class="customer-info">
                <strong>Thông tin người nhận:</strong>
                <p>Tên KH: {{ $order->ship_name }}</p>
                <p>SĐT: {{ $order->ship_phone }}</p>
                <p>Địa chỉ: {{ $order->ship_address ?? 'Không có thông tin' }}</p>
            </div>
            <div class="order-details">
                <strong>Thông tin thanh toán:</strong>
                <p>PT Thanh toán: COD (Thanh toán khi nhận hàng)</p>
                @php
                    $statusLabels = [
                        'pending' => 'Chờ xử lý',
                        'shipped' => 'Đang giao',
                        'completed' => 'Đã giao',
                        'cancelled' => 'Đã hủy',
                        'delivery_failed' => 'Giao hàng thất bại',
                    ];
                @endphp
                <p>Trạng thái: {{ $statusLabels[$order->status] ?? $order->status }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên sản phẩm</th>
                    <th class="center">Phân loại</th>
                    <th class="center">SL</th>
                    <th class="right">Đơn giá</th>
                    <th class="right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>{{ $item->variant->product->name ?? 'Sản phẩm đã bị xóa' }}</td>
                        <td class="center">{{ $item->variant->color }} / {{ $item->variant->size }}</td>
                        <td class="center">{{ $item->quantity }}</td>
                        <td class="right">{{ number_format($item->price, 0, ',', '.') }}₫</td>
                        <td class="right">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="5" class="right">Tổng Cộng:</td>
                    <td class="right">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <p>Cảm ơn quý khách đã tin mua sản phẩm của Lumiere!</p>
            <p><em>(Xin vui lòng giữ lại hóa đơn để tiện cho việc đổi trả hàng trong vòng 7 ngày)</em></p>
        </div>
    </div>
</body>
</html>
