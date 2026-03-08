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

// Trang collection / shop
Route::get('/collection', [CollectionController::class, 'index'])->name('collection');

// Trang chi tiết sản phẩm
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

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

require __DIR__.'/auth.php';
