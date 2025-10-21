<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\User;

test('can remove an item from the cart', function () {

    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
    ]);

    $cartItemA = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'price' => 10,
        'quantity' => 2,
        'total' => 20,
        'taxes' => json_encode([
            [
            'name' => 'IVA',
            'percentage' => 15
            ]
        ]),
        'total_with_taxes' => 2 * 10 * (1 + 0.15), // 23
        'computed_taxes' => 2 * 10 * 0.15, // 3
    ]);

    $cartItemB = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'price' => 20,
        'quantity' => 3,
        'total' => 60,
        'taxes' => json_encode([[
            'name' => 'IVA',
            'percentage' => 15
        ]]),
        'total_with_taxes' => 3 * 20 * (1 + 0.15), // 69
        'computed_taxes' => 3 * 20 * 0.15, // 9
    ]);

    expect($cart->items)->toHaveCount(2);
    expect($cart->fresh()->total_amount)->toBe(92.0);
    expect($cart->fresh()->total_with_taxes)->toBe(80.0);
    expect($cart->fresh()->total_without_taxes)->toBe(0.0);
    expect($cart->fresh()->total_computed_taxes)->toBe(12.0);


    $this->post(route('cart.items.remove', [
        'ui_cart_id' => $uiCartId,
        'item_id' => $cartItemB->id,
    ]))->assertStatus(200);

    expect($cart->fresh()->items)->toHaveCount(1);
    expect($cart->fresh()->total_amount)->toBe(23.0);
    expect($cart->fresh()->total_with_taxes)->toBe(20.0);
    expect($cart->fresh()->total_without_taxes)->toBe(0.0);
    expect($cart->fresh()->total_computed_taxes)->toBe(3.0);

});

it('removes an item from order if it is removed from cart', function () {
    $user = User::factory()->create();
    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
        'user_id' => $user->id
    ]);

    $cartItemA = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'price' => 10,
        'quantity' => 2,
        'total' => 20,
        'taxes' => json_encode([
            [
            'name' => 'IVA',
            'percentage' => 15
            ]
        ]),
        'total_with_taxes' => 2 * 10 * (1 + 0.15), // 23
        'computed_taxes' => 2 * 10 * 0.15, // 3
    ]);

    $cartItemB = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'price' => 20,
        'quantity' => 3,
        'total' => 60,
        'taxes' => json_encode([[
            'name' => 'IVA',
            'percentage' => 15
        ]]),
        'total_with_taxes' => 3 * 20 * (1 + 0.15), // 69
        'computed_taxes' => 3 * 20 * 0.15, // 9
    ]);

    $order = Order::placeFor($user, $cart);

    expect($order->orderItems)->toHaveCount(2);

    expect($order->orderItems[0]->id)->toBe($order->orderItems[0]->cart_item_id);
    expect($order->orderItems[1]->id)->toBe($order->orderItems[1]->cart_item_id);

    expect($order->fresh()->total_amount)->toBe(92.0);
    expect($order->fresh()->total_with_taxes)->toBe(80.0);
    expect($order->fresh()->total_without_taxes)->toBe(0.0);
    expect($order->fresh()->total_computed_taxes)->toBe(12.0);

    $this->post(route('cart.items.remove', [
        'ui_cart_id' => $uiCartId,
        'item_id' => $cartItemB->id,
    ]))->assertStatus(200);

    expect($order->fresh()->orderItems)->toHaveCount(1);
    expect($order->fresh()->total_amount)->toBe(23.0);
    expect($order->fresh()->total_with_taxes)->toBe(20.0);
    expect($order->fresh()->total_without_taxes)->toBe(0.0);
    expect($order->fresh()->total_computed_taxes)->toBe(3.0);


    //expect($order->fresh()->total_amount)->toBe(23);
    //expect($order->fresh()->orderItems)->toHaveCount(1);

    //expect($order->fresh()->total_amount)->toBe(23);

    /* expect($order->fresh()->orderItems)->toHaveCount(1);
    expect($order->fresh()->total_amount)->toBe(23.0);
    expect($order->fresh()->total_with_taxes)->toBe(20.0);
    expect($order->fresh()->total_without_taxes)->toBe(0.0);
    expect($order->fresh()->total_computed_taxes)->toBe(3.0); */
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
