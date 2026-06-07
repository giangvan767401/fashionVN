<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Notification;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Log;

class QRPaymentController extends Controller
{
    // Sinh QR cho trang thanh toán
    public function generateQR(Order $order)
    {
        $bankAccount = env('BANK_ACCOUNT_NUMBER');
        $bankName    = env('BANK_NAME');       // MB, VCB, TCB...
        $amount      = (int) $order->total_amount;
        $content     = env('SEPAY_MATCH_PATTERN', 'DH') . str_pad($order->id, 3, '0', STR_PAD_LEFT);
        // Nội dung ví dụ: "DH001" — SePay sẽ dùng để khớp đơn hàng

        // Dùng SePay QR Image API — không cần public URL, chỉ cần params
        $qrUrl = "https://qr.sepay.vn/img?" . http_build_query([
            'acc'      => $bankAccount,
            'bank'     => $bankName,
            'amount'   => $amount,
            'des'      => $content,
            'template' => 'compact',
        ]);

        return view('payment.qr', compact('order', 'qrUrl', 'content'));
    }

    // Webhook nhận từ SePay khi có tiền về
    public function webhook(Request $request)
    {
        Log::info('SePay Webhook received', $request->all());

        // Hỗ trợ lấy token từ header Authorization dạng 'Apikey <token>' hoặc 'Bearer <token>'
        $authorization = $request->header('Authorization', '');
        $token = null;
        if (preg_match('/(?:Apikey|Bearer)\s+(.+)/i', $authorization, $matches)) {
            $token = trim($matches[1]);
        }

        Log::info('Token nhận được: ' . ($token ?? 'NULL'));
        Log::info('Token trong env: ' . env('SEPAY_WEBHOOK_TOKEN'));

        if ($token !== env('SEPAY_WEBHOOK_TOKEN')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $content        = $request->input('content', '');       // nội dung CK
        $transferAmount = $request->input('transferAmount', 0);
        $transferType   = $request->input('transferType');       // "in" = tiền vào

        // Chỉ xử lý tiền vào
        if ($transferType !== 'in') {
            return response()->json(['success' => true]);
        }

        // Tìm mã đơn hàng trong nội dung chuyển khoản
        $pattern = env('SEPAY_MATCH_PATTERN', 'DH');
        if (preg_match('/' . $pattern . '\s*(\d+)/i', $content, $matches)) {
            $orderId = (int) $matches[1];
            $order   = Order::find($orderId);

            if ($order && $order->payment_status !== 'paid') {
                // Kiểm tra số tiền khớp (cho phép chênh lệch 1000đ)
                if (abs($transferAmount - $order->total_amount) <= 1000) {
                    $order->update([
                        'status'         => 'processing',
                        'payment_status' => 'paid',
                    ]);

                    if ($order->coupon) {
                        $order->coupon->increment('used_count');
                    }
                    if ($order->points_redeemed > 0 && $order->user) {
                        $order->user->decrement('loyalty_points', $order->points_redeemed);
                    }

                    // Lưu lịch sử thanh toán
                    PaymentTransaction::create([
                        'order_id'         => $order->id,
                        'gateway'          => 'sepay',
                        'transaction_code' => $request->input('transactionCode') ?? 'SEPAY-' . strtoupper(uniqid()),
                        'amount'           => $transferAmount,
                        'currency'         => 'VND',
                        'status'           => 'success',
                        'gateway_response' => $request->all(),
                        'paid_at'          => now(),
                    ]);

                    // Tạo thông báo cho khách (sử dụng Model Notification tùy chỉnh của app)
                    if ($order->user) {
                        Notification::create([
                            'user_id'    => $order->user->id,
                            'type'       => 'payment_success',
                            'title'      => 'Thanh toán thành công',
                            'body'       => "Đơn hàng #{$order->order_number} đã được xác nhận thanh toán.",
                            'action_url' => '/profile/orders/' . $order->id,
                            'is_read'    => 0,
                            'created_at' => now(),
                        ]);
                    }
                }
            }
        }

        return response()->json(['success' => true]);
    }
}
