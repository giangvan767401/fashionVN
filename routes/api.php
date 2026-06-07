<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QRPaymentController;
use App\Models\Order;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Các route API không yêu cầu session/CSRF. SePay gọi webhook vào đây.
*/

// SePay webhook — SePay server gọi POST vào đây khi có tiền về
Route::post('/sepay/webhook', [QRPaymentController::class, 'webhook']);

// Kiểm tra trạng thái đơn hàng (dùng cho JS polling trên trang QR)
Route::get('/orders/{order}/status', function (Order $order) {
    return response()->json(['status' => $order->status]);
});
