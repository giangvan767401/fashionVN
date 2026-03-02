<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PageController;

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Trang collection / shop
Route::get('/collection', [CollectionController::class, 'index'])->name('collection');

// Trang chi tiết sản phẩm
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

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
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

