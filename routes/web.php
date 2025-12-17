<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CollectionPageController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\AboutPageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductPageController;
use App\Http\Controllers\ProductsPageController;
use App\Http\Controllers\CollectionsPageController;
use App\Http\Controllers\UserInfoEntryController;
use App\Http\Controllers\ContactPageController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', HomePageController::class)->name('storefront.home');
Route::get('/products', ProductsPageController::class)->name('storefront.products');
Route::get('/products/{product:slug}', ProductPageController::class)->name('storefront.product');
Route::get('/collections', CollectionsPageController::class)->name('storefront.collections');
Route::get('/collections/{collection:slug}', CollectionPageController::class)->name('storefront.collection');
Route::get('/about', AboutPageController::class)->name('storefront.about');
Route::get('/contact', [ContactPageController::class,'index'])->name('storefront.contact');


Route::post('/cart/create', [CartController::class, 'create'])->name('cart.create');
Route::post('/cart/show', [CartController::class, 'show'])->name('cart.show');
Route::post('/cart/items/addOrUpdate', [CartItemController::class, 'addOrUpdate'])->name('cart.items.addOrUpdate');
Route::post('/cart/items/remove', [CartItemController::class, 'remove'])->name('cart.items.remove');
Route::post('/cart/empty', [CartController::class, 'empty'])->name('cart.empty');


Route::post('/contact', [ContactPageController::class, 'sendMessage'])
->middleware([HandlePrecognitiveRequests::class])
->name('storefront.send-message');

Route::get('/login', function () {
    return Inertia::location('/customer/login'); // redirect()->route('filament.customer.auth.login');
})->name('login');

Route::get('/payments/confirm', [PaymentController::class, 'confirm'])->name('payments.confirm');

Route::middleware([Authenticate::class])->group(function () {
    Route::get('/checkout', CheckoutController::class)->name('storefront.checkout');
    Route::post('/orders/cancel', [OrderController::class, 'cancelOrder'])->name('storefront.orders.cancel');
    Route::post('/user-info', [UserInfoEntryController::class, 'store'])->name('storefront.user-info-entry.store');
    Route::put('/user-info/{id}', [UserInfoEntryController::class, 'update'])->name('storefront.user-info-entry.update');
    Route::post('/shipping-info/use-billing', [UserInfoEntryController::class, 'useBillingAsShipping'])->name('storefront.user-info-entry.use-billing-as-shipping');

});
