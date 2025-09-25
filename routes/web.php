<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductsPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('storefront.home');
Route::get('/products', ProductsPageController::class)->name('storefront.products');

Route::post('/cart/create', [CartController::class, 'create'])->name('cart.create');
Route::post('/cart/show', [CartController::class, 'show'])->name('cart.show');
