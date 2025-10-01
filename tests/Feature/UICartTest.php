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
    expect(session('cart'))->toBeInstanceOf(Cart::class);
    expect(session('cart')->ui_cart_id)->toBe($uiCartId);

});

test('can get a cart from the ui stored in local storage', function () {

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
        ]);
});
