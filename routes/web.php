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
Route::post('/cart/add', [\App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [\App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');

// Yêu thích
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle')->middleware('auth');
Route::delete('/wishlist/remove/{variantId}', [WishlistController::class, 'remove'])->name('wishlist.remove')->middleware('auth');

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
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');
    
    // Quản lý người dùng
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

    // Quản lý danh mục
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->names('categories');
    Route::patch('/categories/{category}/toggle-status', [\App\Http\Controllers\Admin\CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

    // Quản lý sản phẩm
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)->names('products');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/info', [ProfileController::class, 'editInfo'])->name('profile.info');
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

