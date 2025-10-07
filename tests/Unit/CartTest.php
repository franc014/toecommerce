<?php

use App\Enums\StockControlModes;
use App\Exceptions\ProductOutOfStockException;
use App\Models\AppSettings;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tax;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Exceptions;

function createCartWithoutItem(array $productData, $isVariant = false)
{

    if ($isVariant) {
        $purchasable = ProductVariant::factory([
            'product_id' => Product::factory()
        ])->published()->create($productData);
    } else {
        $purchasable = Product::factory()->published()->create($productData);
    }

    $cart = Cart::factory()->create();
    return [$purchasable, $cart];
}

function createCartWithItem(array $data, $isVariant = false)
{

    if ($isVariant) {
        $purchasable = ProductVariant::factory()->published()->create($data);
        $cart = Cart::factory()->has(CartItem::factory()->count(1)->state([
            'title' => $purchasable->title,
            'slug' => $purchasable->slug,
            'price' => $purchasable->price,
            'quantity' => 4,
            'total' => 4 * $purchasable->price,
            'color' => $purchasable->color,
            'sizes' => $purchasable->sizes
        ]), 'items')->create();
    } else {
        $purchasable = Product::factory()->published()->create($data);
    }

    $cart =  Cart::factory()->has(CartItem::factory()->count(1)->state([
        'purchasable_id' => $purchasable->id,
        'purchasable_type' => Product::class,
        'title' => $purchasable->title,
        'slug' => $purchasable->slug,
        'price' => $purchasable->price,
        'quantity' => 4,
        'total' => 4 * $purchasable->price
    ]), 'items')->create();

    return [$purchasable, $cart];
}

test('can get a cart item by purchasable_id', function () {
    [$purchasable, $cart] = createCartWithItem([
     'price' => 50,
     'title' => 'Product 1',
     'slug' => 'product-1',
    ]);

    $item = $cart->getItemByPurchasableId($purchasable->id);

    expect($item)->toBeInstanceOf(CartItem::class);

    expect($item->title)->toBe($purchasable->title);
    expect($item->slug)->toBe($purchasable->slug);
    expect($item->price)->toBe($purchasable->price);
    expect($item->quantity)->toBe(4);
    expect($item->total)->toBe(4 * $purchasable->price);

});



test('getting a cart item by id', function () {

    [$product, $cart] = createCartWithItem([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 50,
    ]);

    expect($cart->itemById($product->id))->toBeInstanceOf(CartItem::class);
    expect($cart->itemById($product->id)->id)->toBe($cart->items->first()->id);
});





test('getting the subtotal', function () {
    $cart = Cart::factory()->has(CartItem::factory()->count(2)->state(new Sequence([
        'price' => 40.00,
        'quantity' => 3,
        'total' => 120.00
    ], [
        'price' => 50.00,
        'quantity' => 2,
        'total' => 100.00
    ])), 'items')->create();

    expect($cart->subtotal)->toBe(220.0);
});

test('getting the subtotal in dollars', function () {
    $cart = Cart::factory()->has(CartItem::factory()->count(2)->state(new Sequence([
        'price' => 40.00,
        'quantity' => 3,
        'total' => 120.00
    ], [
        'price' => 50.00,
        'quantity' => 2,
        'total' => 100.00
    ])), 'items')->create();

    expect($cart->subtotalInDollars)->toBe('$220');
});

test('getting the total with taxes', function () {
    $cart = Cart::factory()->has(CartItem::factory()->count(2)->state(new Sequence([
        'price' => 40.00,
        'quantity' => 3,
        'total' => 120.00,
        'total_with_taxes' => 140.00
    ], [
        'price' => 50.00,
        'quantity' => 2,
        'total' => 100.00,
        'total_with_taxes' => 120.00
    ])), 'items')->create();

    ray($cart);

    expect($cart->total_with_taxes)->toBe(260.0);
});

test('getting the total with taxes in dollars', function () {
    $cart = Cart::factory()->has(CartItem::factory()->count(2)->state(new Sequence([
        'price' => 40.00,
        'quantity' => 3,
        'total' => 120.00,
        'total_with_taxes' => 140.00
    ], [
        'price' => 50.00,
        'quantity' => 2,
        'total' => 100.00,
        'total_with_taxes' => 120.00
    ])), 'items')->create();
    expect($cart->total_with_taxes_in_dollars)->toBe('$260');
});

test('getting the total count of items in the cart', function () {
    $cart = Cart::factory()->has(CartItem::factory()->count(2)->state(new Sequence([
        'price' => 40.00,
        'quantity' => 3,
        'total' => 120.00
    ], [
        'price' => 50.00,
        'quantity' => 2,
        'total' => 100.00
    ])), 'items')->create();

    expect($cart->items_count)->toBe(5);
});
