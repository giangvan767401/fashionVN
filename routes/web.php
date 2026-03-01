<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);
Route::get('/collection', [CollectionController::class, 'index'])->name('collection');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('product.show');

// Informational Pages
Route::get('/su-menh', [PageController::class, 'mission'])->name('page.mission');
Route::get('/phat-trien-ben-vung', [PageController::class, 'sustainability'])->name('page.sustainability');
Route::get('/cau-hoi-thuong-gap', [PageController::class, 'faq'])->name('page.faq');
Route::get('/lien-he', [PageController::class, 'contact'])->name('page.contact');
