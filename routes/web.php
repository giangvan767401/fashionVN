<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\WishlistController;

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');


// Chuyển đổi ngôn ngữ
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'vi'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

// Trang collection / shop
Route::get('/collection', [CollectionController::class, 'index'])->name('collection');

// Trang chi tiết sản phẩm
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/product/{slug}/try-on', [\App\Http\Controllers\TryOnController::class, 'index'])->name('tryon.index');
Route::post('/try-on/process', [\App\Http\Controllers\TryOnController::class, 'process'])->name('tryon.process');

// Giỏ hàng
Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [\App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [\App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');

// Thanh toán
Route::get('/checkout', [\App\Http\Controllers\CheckoutController::class, 'info'])->middleware(['auth', 'verified'])->name('checkout');
Route::post('/checkout', [\App\Http\Controllers\CheckoutController::class, 'storeInfo'])->middleware(['auth', 'verified'])->name('checkout.store_info');
Route::get('/checkout/shipping', [\App\Http\Controllers\CheckoutController::class, 'shipping'])->middleware(['auth', 'verified'])->name('checkout.shipping');
Route::post('/checkout/shipping', [\App\Http\Controllers\CheckoutController::class, 'storeShipping'])->middleware(['auth', 'verified'])->name('checkout.store_shipping');
Route::get('/checkout/payment', [\App\Http\Controllers\CheckoutController::class, 'payment'])->middleware(['auth', 'verified'])->name('checkout.payment');
Route::post('/checkout/payment', [\App\Http\Controllers\CheckoutController::class, 'storePayment'])->middleware(['auth', 'verified'])->name('checkout.store_payment');
Route::get('/checkout/success/{id}', [\App\Http\Controllers\CheckoutController::class, 'success'])->middleware(['auth', 'verified'])->name('checkout.success');

// Áp dụng/Hủy mã giảm giá
Route::post('/coupon/apply', [\App\Http\Controllers\CouponController::class, 'apply'])->middleware(['auth', 'verified'])->name('coupon.apply');
Route::post('/coupon/remove', [\App\Http\Controllers\CouponController::class, 'remove'])->middleware(['auth', 'verified'])->name('coupon.remove');

// Sử dụng điểm tích lũy
Route::post('/checkout/toggle-points', [\App\Http\Controllers\LoyaltyController::class, 'togglePoints'])->middleware(['auth', 'verified'])->name('checkout.toggle-points');

// MoMo callback (không cần auth vì MoMo gọi về từ server bên ngoài)
Route::get('/momo/confirm-scan/{order_number}', [\App\Http\Controllers\MomoController::class, 'confirmScan'])->name('momo.confirm_scan');
Route::post('/momo/confirm-scan/{order_number}/execute', [\App\Http\Controllers\MomoController::class, 'executeConfirmScan'])->name('momo.execute_confirm_scan');
Route::get('/api/orders/check-status/{order_number}', [\App\Http\Controllers\MomoController::class, 'checkStatus'])->name('momo.check_status');
Route::get('/momo/return', [\App\Http\Controllers\MomoController::class, 'returnPayment'])->name('momo.return');
Route::post('/momo/notify', [\App\Http\Controllers\MomoController::class, 'notify'])->name('momo.notify')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// PayOS callback (không cần auth đối với webhook nhận từ PayOS)
Route::get('/payos/demo/{order_id}', [\App\Http\Controllers\PayOSController::class, 'demo'])->name('payos.demo');
Route::get('/payos/confirm-scan/{order_id}', [\App\Http\Controllers\PayOSController::class, 'confirmScan'])->name('payos.confirm_scan');
Route::post('/payos/confirm-scan/{order_id}/execute', [\App\Http\Controllers\PayOSController::class, 'executeConfirmScan'])->name('payos.execute_confirm_scan');
Route::get('/payos/return/{order_id}', [\App\Http\Controllers\PayOSController::class, 'returnPayment'])->name('payos.return');
Route::get('/payos/cancel/{order_id}', [\App\Http\Controllers\PayOSController::class, 'cancelPayment'])->name('payos.cancel');
Route::post('/webhook/payos', [\App\Http\Controllers\PayOSController::class, 'webhook'])->name('payos.webhook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// VNPAY payment gateway
Route::get('/vnpay/return', [\App\Http\Controllers\VNPayController::class, 'returnPayment'])->name('vnpay.return');
Route::get('/vnpay/cancel/{order_id}', [\App\Http\Controllers\VNPayController::class, 'cancelPayment'])->name('vnpay.cancel');
Route::get('/vnpay/confirm-scan/{order_id}', [\App\Http\Controllers\VNPayController::class, 'confirmScan'])->name('vnpay.confirm_scan');
Route::post('/vnpay/confirm-scan/{order_id}/execute', [\App\Http\Controllers\VNPayController::class, 'executeConfirmScan'])->name('vnpay.execute_confirm_scan');
Route::get('/vnpay/demo/{order_id}', [\App\Http\Controllers\VNPayController::class, 'demo'])->middleware(['auth', 'verified'])->name('vnpay.demo');
Route::post('/vnpay/demo/confirm', [\App\Http\Controllers\VNPayController::class, 'demoConfirm'])->middleware(['auth', 'verified'])->name('vnpay.demo.confirm');
Route::post('/vnpay/demo/cancel', [\App\Http\Controllers\VNPayController::class, 'demoCancel'])->middleware(['auth', 'verified'])->name('vnpay.demo.cancel');
Route::get('/momo/demo', [\App\Http\Controllers\MomoController::class, 'demo'])->middleware(['auth', 'verified'])->name('momo.demo');
Route::post('/momo/demo/confirm', [\App\Http\Controllers\MomoController::class, 'demoConfirm'])->middleware(['auth', 'verified'])->name('momo.demo.confirm');
Route::post('/momo/demo/cancel', [\App\Http\Controllers\MomoController::class, 'demoCancel'])->middleware(['auth', 'verified'])->name('momo.demo.cancel');

// SePay QR Payment — trang hiển thị mã QR cho khách hàng
Route::get('/orders/{order}/qr-payment', [\App\Http\Controllers\QRPaymentController::class, 'generateQR'])
     ->middleware(['auth', 'verified'])
     ->name('payment.qr');

// Yêu thích
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle')->middleware(['auth', 'verified']);
Route::delete('/wishlist/remove/{variantId}', [WishlistController::class, 'remove'])->name('wishlist.remove')->middleware(['auth', 'verified']);

// Các trang tĩnh
Route::get('/mission', [PageController::class, 'mission'])->name('page.mission');
Route::get('/sustainability', [PageController::class, 'sustainability'])->name('page.sustainability');
Route::get('/faq', [PageController::class, 'faq'])->name('page.faq');
Route::get('/contact', [PageController::class, 'contact'])->name('page.contact');

// Dashboard (cần login)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');

    // Admin Chat
    Route::get('/chat', [\App\Http\Controllers\Admin\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{user}', [\App\Http\Controllers\Admin\ChatController::class, 'show'])->name('chat.show');
    Route::get('/chat/{user}/fetch', [\App\Http\Controllers\Admin\ChatController::class, 'fetch'])->name('chat.fetch');
    Route::post('/chat/{user}/store', [\App\Http\Controllers\Admin\ChatController::class, 'store'])->name('chat.store');

    Route::get('/analytics', [\App\Http\Controllers\Admin\AdminController::class, 'analytics'])->name('analytics');
    Route::get('/api/analytics', [\App\Http\Controllers\Admin\AdminController::class, 'getAnalyticsData'])->name('analytics.data');

    // Quản lý người dùng
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

    // Quản lý danh mục
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->names('categories');
    Route::patch('/categories/{category}/toggle-status', [\App\Http\Controllers\Admin\CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

    Route::patch('/products/{product}/toggle-status', [\App\Http\Controllers\Admin\ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)->names('products');

    // Quản lý đơn hàng
    Route::get('/orders/export', [\App\Http\Controllers\Admin\OrderController::class, 'export'])->name('orders.export');
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/print', [\App\Http\Controllers\Admin\OrderController::class, 'print'])->name('orders.print');
    Route::patch('/orders/{id}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::delete('/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'destroy'])->name('orders.destroy');

    // Quản lý voucher / coupon
    Route::patch('/coupons/{coupon}/toggle-status', [\App\Http\Controllers\Admin\CouponController::class, 'toggleStatus'])->name('coupons.toggle-status');
    Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class)->names('coupons');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/info', [ProfileController::class, 'editInfo'])->name('profile.info');
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Đơn hàng của tôi
    Route::get('/profile/orders', [\App\Http\Controllers\UserOrderController::class, 'index'])->name('profile.orders');
    Route::get('/profile/orders/{id}', [\App\Http\Controllers\UserOrderController::class, 'show'])->name('profile.orders.show');
    Route::post('/profile/orders/{id}/cancel', [\App\Http\Controllers\UserOrderController::class, 'cancel'])->name('profile.orders.cancel');
    Route::post('/profile/orders/{id}/confirm-received', [\App\Http\Controllers\UserOrderController::class, 'confirmReceived'])->name('profile.orders.confirm-received');

    // Đánh giá sản phẩm
    Route::post('/reviews', [\App\Http\Controllers\UserReviewController::class, 'store'])->name('reviews.store');

    // Thông báo
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'readAll'])->name('notifications.read-all');

    // User Chat
    Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/fetch', [\App\Http\Controllers\ChatController::class, 'fetch'])->name('chat.fetch');
    Route::post('/chat/store', [\App\Http\Controllers\ChatController::class, 'store'])->name('chat.store');
});

// Chatbot AI
Route::post('/chatbot/reply', [\App\Http\Controllers\ChatbotController::class, 'reply'])->name('chatbot.reply');

require __DIR__ . '/auth.php';

