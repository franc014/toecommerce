<?php

use App\Models\Cart;
use App\Models\CartItem;

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
            'items' => $cart->items->toArray(),
            'cart_aggregation' => [
                'total_without_taxes_in_dollars' => $cart->total_without_taxes_in_dollars,
                'total_with_taxes_in_dollars' => $cart->total_with_taxes_in_dollars,
                'total_computed_taxes_in_dollars' => $cart->total_computed_taxes_in_dollars,
                'total_in_dollars' => $cart->total_in_dollars,
                'items_count' => $cart->items_count,
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
    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create([
        'ui_cart_id' => $uiCartId,
    ]);

    expect($cart->items)->toHaveCount(2);

    $this->post(route('cart.empty', [
        'id' => $uiCartId,
    ]))->assertStatus(200);

    expect($cart->fresh()->items)->toHaveCount(0);

});
