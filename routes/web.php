<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductsPageController;
use App\Http\Controllers\UserInfoEntryController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', HomeController::class)->name('storefront.home');
Route::get('/products', ProductsPageController::class)->name('storefront.products');

Route::post('/cart/create', [CartController::class, 'create'])->name('cart.create');
Route::post('/cart/show', [CartController::class, 'show'])->name('cart.show');
Route::post('/cart/items/addOrUpdate', [CartItemController::class, 'addOrUpdate'])->name('cart.items.addOrUpdate');
Route::post('/cart/items/remove', [CartItemController::class, 'remove'])->name('cart.items.remove');
Route::post('/cart/empty', [CartController::class, 'empty'])->name('cart.empty');

Route::get('/login', function () {
    return Inertia::location('/customer/login'); // redirect()->route('filament.customer.auth.login');
})->name('login');

Route::get('/payments/confirm', [PaymentController::class, 'confirm'])->name('payments.confirm');

Route::middleware([Authenticate::class])->group(function () {
    Route::get('/checkout', CheckoutController::class)->name('storefront.checkout');
    Route::post('/user-info', [UserInfoEntryController::class, 'store'])->name('storefront.user-info-entry.store');
    Route::put('/user-info/{id}', [UserInfoEntryController::class, 'update'])->name('storefront.user-info-entry.update');
    Route::post('/shipping-info/use-billing', [UserInfoEntryController::class, 'useBillingAsShipping'])->name('storefront.user-info-entry.use-billing-as-shipping');

});
