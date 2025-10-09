<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductsPageController;

use Illuminate\Support\Facades\Route;

use Illuminate\Auth\Middleware\Authenticate;

Route::get('/', HomeController::class)->name('storefront.home');
Route::get('/products', ProductsPageController::class)->name('storefront.products');

Route::post('/cart/create', [CartController::class, 'create'])->name('cart.create');
Route::post('/cart/show', [CartController::class, 'show'])->name('cart.show');
Route::post('/cart/items/addOrUpdate', [CartItemController::class, 'addOrUpdate'])->name('cart.items.addOrUpdate');
Route::post('/cart/items/remove', [CartItemController::class, 'remove'])->name('cart.items.remove');
Route::post('/cart/empty', [CartController::class, 'empty'])->name('cart.empty');

Route::get('/login', function () {
    return redirect()->route('filament.customer.auth.login');//route to filament...
})->name('login');

Route::middleware(Authenticate::class)->get('/checkout', CheckoutController::class)->name('storefront.checkout');
