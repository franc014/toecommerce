<?php

use App\Models\Cart;
use App\Models\CartItem;

test('can remove an item from the cart', function () {

    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create([
        'ui_cart_id' => $uiCartId,
    ]);

    expect($cart->items)->toHaveCount(2);

    $itemToRemove = $cart->items->first();

    $this->post(route('cart.items.remove', [
        'ui_cart_id' => $uiCartId,
        'item_id' => $itemToRemove->id,
    ]))->assertStatus(200);

    expect($cart->fresh()->items)->toHaveCount(1);
});

test('can not remove an item from the cart if the cart does not exist', function () {
    $this->post(route('cart.items.remove', [
        'ui_cart_id' => fake()->uuid(),
        'item_id' => 1,
    ]))->assertStatus(404);
});

test('cart ui id is required', function () {

    $this->post(route('cart.items.remove', [
        'ui_cart_id' => '',
        'item_id' => 2,
    ]))->assertInvalid(['ui_cart_id']);
});

test('cart ui id should be a valid uuid', function () {

    $this->post(route('cart.items.remove', [
        'ui_cart_id' => 'abc',
        'item_id' => 2,
    ]))->assertInvalid(['ui_cart_id']);
});


test('item id is required', function () {
    $uiCartId = fake()->uuid();
    Cart::factory()->has(CartItem::factory(), 'items')->create([
        'ui_cart_id' => $uiCartId,
    ]);
    $this->post(route('cart.items.remove', [
        'ui_cart_id' => $uiCartId,
        'item_id' => '',
    ]))->assertInvalid(['item_id']);
});

test('item id should be integer', function () {
    $uiCartId = fake()->uuid();
    Cart::factory()->has(CartItem::factory(), 'items')->create([
        'ui_cart_id' => $uiCartId,
    ]);
    $this->post(route('cart.items.remove', [
        'ui_cart_id' => fake()->uuid(),
        'item_id' => 'abc',
    ]))->assertInvalid(['item_id']);
});
