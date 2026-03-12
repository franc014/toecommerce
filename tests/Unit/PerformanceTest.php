<?php

use App\Http\Resources\CartItemResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    DB::disableQueryLog();
});

test('product listing generates minimal queries', function () {
    Product::factory()->published()->count(20)->create();

    DB::enableQueryLog();

    $products = Product::take(20)->get();

    $queryCount = count(DB::getQueryLog());

    DB::disableQueryLog();

    expect($queryCount)->toBe(1, "Expected 1 query for product listing, got {$queryCount}");
});

test('product listing does not trigger accessor queries without explicit access', function () {
    $products = Product::factory()->published()->count(10)->create();

    DB::enableQueryLog();

    $fetched = Product::take(10)->get();

    $queryCount = count(DB::getQueryLog());

    DB::disableQueryLog();

    expect($queryCount)->toBe(1, 'Fetching products should be 1 query without $appends');
    expect($fetched->count())->toBe(10);
});

test('product toArray does not automatically include appended accessor data', function () {
    $product = Product::factory()->published()->create([
        'price' => 100.00,
    ]);

    $array = $product->toArray();

    expect($array)->not->toHaveKey('price_in_dollars')
        ->and($array)->not->toHaveKey('has_discounts')
        ->and($array)->not->toHaveKey('discounted_price_in_dollars');
});

test('product accessors still work when called explicitly', function () {
    $product = Product::factory()->published()->create([
        'price' => 100.00,
    ]);

    expect($product->price_in_dollars)->toBe('$100')
        ->and($product->has_discounts)->toBeFalse();
});

test('product variant toArray does not automatically include appended accessor data', function () {
    $product = Product::factory()->published()->create();
    $variant = ProductVariant::factory()->published()->create([
        'product_id' => $product->id,
        'price' => 50.00,
    ]);

    $array = $variant->toArray();

    expect($array)->not->toHaveKey('price_in_dollars')
        ->and($array)->not->toHaveKey('formatted_variation')
        ->and($array)->not->toHaveKey('has_discounts');
});

test('product variant accessors still work when called explicitly', function () {
    $product = Product::factory()->published()->create();
    $variant = ProductVariant::factory()->published()->create([
        'product_id' => $product->id,
        'price' => 50.00,
        'variation' => ['color' => 'red'],
    ]);

    expect($variant->price_in_dollars)->toBe('$50')
        ->and($variant->formatted_variation)->toBeString();
});

test('user toArray does not automatically include has_billing_info', function () {
    $user = User::factory()->create();

    $array = $user->toArray();

    expect($array)->not->toHaveKey('has_billing_info')
        ->and($array)->not->toHaveKey('has_shipping_info');
});

test('user accessors still work when called explicitly', function () {
    $user = User::factory()->create();

    expect($user->has_billing_info)->toBeFalse()
        ->and($user->has_shipping_info)->toBeFalse();
});

test('cart item toArray includes mapped fields', function () {
    $product = Product::factory()->published()->create(['price' => 100.00]);
    $cart = Cart::factory()->create();

    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'purchasable_id' => $product->id,
        'purchasable_type' => Product::class,
        'price' => 100.00,
        'quantity' => 2,
    ]);

    $array = CartItemResource::make($cartItem)->toArray(new Request);

    expect($array)->toHaveKey('id')
        ->and($array)->toHaveKey('title')
        ->and($array)->toHaveKey('price_in_dollars')
        ->and($array)->toHaveKey('total_in_dollars')
        ->and($array)->toHaveKey('quantity')
        ->and($array)->toHaveKey('image_url');
});

test('cart retains appends for API responses', function () {
    $cart = Cart::factory()->create();

    $array = $cart->toArray();

    expect($array)->toHaveKey('total_without_taxes_in_dollars')
        ->and($array)->toHaveKey('total_with_taxes_in_dollars')
        ->and($array)->toHaveKey('items_count');
});

test('fetching multiple products with relationships is efficient', function () {
    $tax = Tax::factory()->create(['percentage' => 15]);
    $discount = Discount::factory()->active()->create(['percentage' => 10]);

    $products = Product::factory()->published()->count(10)->create();
    foreach ($products as $product) {
        $product->taxes()->attach($tax);
        $product->discounts()->attach($discount);
    }

    DB::enableQueryLog();

    $fetched = Product::with(['taxes', 'discounts'])->take(10)->get();

    $queryCount = count(DB::getQueryLog());

    DB::disableQueryLog();

    expect($queryCount)->toBeLessThan(5, "Expected fewer than 5 queries with eager loading, got {$queryCount}");
    expect($fetched->count())->toBe(10);
});

test('product variant taxes accessor uses parent product taxes', function () {
    $product = Product::factory()->published()->create();
    $tax = Tax::factory()->create(['percentage' => 15]);
    $product->taxes()->attach($tax);

    $variant = ProductVariant::factory()->published()->create([
        'product_id' => $product->id,
        'price' => 50.00,
    ]);

    expect($variant->taxes)->toHaveCount(1)
        ->and($variant->taxes->first()->percentage)->toBe(15);
});
