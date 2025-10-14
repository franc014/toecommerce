<?php

use App\Models\CartItem;
use App\Models\Product;
use App\Models\Cart;

test('a cart item belongs to a cart', function () {
    $cart = Cart::factory()->create();
    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id
    ]);
    expect($cartItem->cart)->toBeInstanceOf(Cart::class);
    expect($cartItem->cart->id)->toBe($cart->id);
});

test('getting all items from not paid carts by its corresponding product', function () {

    $product = Product::factory()->create();
    $cartItem = CartItem::factory()->create([
        'cart_id' => Cart::factory(),
        'purchasable_id' => $product->id
    ]);

    $cartItem2 = CartItem::factory()->create([
        'cart_id' => Cart::factory(),
        'purchasable_id' => $product->id
    ]);

    $allItems = CartItem::allByProductInOpenCarts($product->id, Product::class);

    $allItems->assertEquals([$cartItem, $cartItem2]);


});

test('getting the price in dollars', function () {
    $cartItem = CartItem::factory()->create([
        'price' => 24.32,
    ]);

    expect($cartItem->priceInDollars)->toBe("$24.32");
});

/* test('getting the total in dollars', function () {
    $cartItem = CartItem::factory()->create([
        'price' => 24.32,
        'quantity' => 2,
        'total' => 48.64
    ]);

    expect($cartItem->totalInDollars)->toBe("$48.64");
}); */

test('getting totals in dollars', function () {
    $taxes = [
        [
            'name' => 'IVA',
            'percentage' => 15,

        ],
        [
            'name' => 'ISD',
            'percentage' => 10,

        ]
    ];

    $totalWithTaxes = 2 * 24.32 * (1 + 0.15 + 0.10);
    $cartItem = CartItem::factory()->create([
        'price' => 24.32,
        'quantity' => 2,
        'total' => 48.64,
        'taxes' => json_encode($taxes),
        'computed_taxes' => 2 * 24.32 * (0.15 + 0.10),
        'total_with_taxes' => $totalWithTaxes
    ]);

    expect($cartItem->totalInDollars)->toBe("$" . 48.64);
    expect($cartItem->computedTaxesInDollars)->toBe("$" . 2 * 24.32 * (0.15 + 0.10));

    $this->assertDatabaseHas('cart_items', [
        'total' => 48.64 * 100,
        'total_with_taxes' => $totalWithTaxes * 100,
        'computed_taxes' => 2 * 24.32 * (0.15 + 0.10) * 100
    ]);
});
