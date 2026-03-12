<?php

use App\Http\Resources\CartItemResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\User;

test('can create a new cart from pinia', function () {

    $uiCartId = fake()->uuid();

    $response = $this->post(route('cart.create'), [
        'id' => $uiCartId,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'ui_cart_id' => $uiCartId,
            'items' => [],
        ]);

    $this->assertDatabaseHas('carts', [
        'ui_cart_id' => $uiCartId,
    ]);

    expect(Cart::count())->toBe(1);
    expect(Cart::first()->items()->count())->toBe(0);

});

test('cart id is required', function () {
    $this->post(route('cart.create'))->assertInvalid(['id']);
});

test('cart id must be a valid uuid', function () {
    $this->post(route('cart.create'), [
        'id' => 'not-a-uuid',
    ])->assertInvalid(['id']);
});

test('can get a cart from the ui', function () {

    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create([
        'ui_cart_id' => $uiCartId,
    ]);

    $this->post(route('cart.show', [
        'id' => $uiCartId,
    ]))->assertStatus(200)
        ->assertJson([
            'ui_cart_id' => $uiCartId,
            'items' => CartItemResource::collection($cart->fresh()->items)->resolve(),
            'cart_aggregation' => [
                'total_without_taxes_in_dollars' => $cart->fresh()->total_without_taxes_in_dollars,
                'total_with_taxes_in_dollars' => $cart->fresh()->total_with_taxes_in_dollars,
                'total_computed_taxes_in_dollars' => $cart->fresh()->total_computed_taxes_in_dollars,
                'total_in_dollars' => $cart->fresh()->total_amount_in_dollars,
                'items_count' => $cart->fresh()->items_count,
            ],
        ]);
});

test('can not get a cart that does not exist', function () {
    $this->post(route('cart.show', [
        'id' => fake()->uuid(),
    ]))->assertStatus(404);
});

test('can not get a cart that has been already paid', function () {
    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create([
        'paid_at' => now()->subDays(1),
    ]);
    $this->post(route('cart.show', [
        'id' => $cart->ui_cart_id,
    ]))->assertStatus(404);
});

test('a cart can be emptied', function () {

    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
    ]);
    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'quantity' => 2,
        'price' => 10,
        'taxes' => json_encode([
            [
                'name' => 'IVA',
                'percentage' => 15,
            ],
        ]),
        'total' => 20,
        'total_with_taxes' => 2 * 10 * (1 + 0.15), // 23
        'computed_taxes' => 2 * 10 * 0.15, // 3
    ]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'quantity' => 3,
        'price' => 20,
        'taxes' => json_encode([
            [
                'name' => 'IVA',
                'percentage' => 15,
            ],
        ]),
        'total' => 60,
        'total_with_taxes' => 3 * 20 * (1 + 0.15), // 69
        'computed_taxes' => 3 * 20 * 0.15, // 9
    ]);

    expect($cart->fresh()->items)->toHaveCount(2);
    expect($cart->fresh()->total_amount)->toBe(92.0);
    expect($cart->fresh()->total_with_taxes)->toBe(80.0);
    expect($cart->fresh()->total_computed_taxes)->toBe(12.0);
    expect($cart->fresh()->total_without_taxes)->toBe(0.0);

    $this->post(route('cart.empty', [
        'id' => $uiCartId,
    ]))->assertStatus(200);

    expect($cart->fresh()->items)->toHaveCount(0);
    expect($cart->fresh()->total_amount)->toBe(0.0);
    expect($cart->fresh()->total_with_taxes)->toBe(0.0);
    expect($cart->fresh()->total_computed_taxes)->toBe(0.0);
    expect($cart->fresh()->total_without_taxes)->toBe(0.0);

});

test('an order is deleted when a cart is emptied', function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create();
    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
        'user_id' => $user->id,
    ]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'quantity' => 2,
        'price' => 10,
        'taxes' => json_encode([
            [
                'name' => 'IVA',
                'percentage' => 15,
            ],
        ]),
        'total' => 20,
        'total_with_taxes' => 2 * 10 * (1 + 0.15), // 23
        'computed_taxes' => 2 * 10 * 0.15, // 3
    ]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'quantity' => 3,
        'price' => 20,
        'taxes' => json_encode([
            [
                'name' => 'IVA',
                'percentage' => 15,
            ],
        ]),
        'total' => 60,
        'total_with_taxes' => 3 * 20 * (1 + 0.15), // 69
        'computed_taxes' => 3 * 20 * 0.15, // 9
    ]);

    $order = Order::placeFor($user, $cart);

    expect($order->fresh()->orderItems)->toHaveCount(2);
    expect($order->fresh()->total_amount)->toBe(92.0);
    expect($order->fresh()->total_with_taxes)->toBe(80.0);
    expect($order->fresh()->total_computed_taxes)->toBe(12.0);
    expect($order->fresh()->total_without_taxes)->toBe(0.0);

    $this->post(route('cart.empty', [
        'id' => $uiCartId,
    ]))->assertStatus(200);

    expect($cart->fresh()->hasOrder())->toBeFalse();

});
