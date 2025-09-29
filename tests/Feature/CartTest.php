<?php

use App\Enums\StockControlModes;
use App\Models\AppSettings;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

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

test('can add a published product to the cart', function () {

    $product = Product::factory()->published()->create([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 20.00,
    ]);
    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create([
        'ui_cart_id' => $uiCartId,
    ]);
    $quantityToAdd = 1;

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $product->id,
        'quantity' => $quantityToAdd,
    ]))->assertStatus(200)
        ->assertJson([
            'ui_cart_id' => $uiCartId,
            'items' => $cart->items->toArray(),
    ]);

    $this->assertDatabaseHas('carts', [
        'ui_cart_id' => $uiCartId,
    ]);

    $this->assertDatabaseHas('cart_items', [
        'cart_id' => $cart->id,
        'title' => $product->title,
        'slug' => $product->slug,
        'purchasable_id' => $product->id,
        'purchasable_type' => Product::class,
        'price' => $product->price * 100,
        'quantity' => $quantityToAdd,
        'taxes' => json_encode($product->taxes->toArray()),
        'total' => $product->price * 100 * $quantityToAdd,
        'total_with_taxes' => ($product->priceWithTaxes() * $quantityToAdd) * 100,
    ]);

});

test('can update an existing cart item quantity', function () {

    $product = Product::factory()->published()->create([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 20.00,
    ]);
    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
    ]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'purchasable_id' => $product->id,
        'purchasable_type' => Product::class,
        'title' => $product->title,
        'slug' => $product->slug,
        'price' => $product->price,
        'quantity' => 4,
        'total' => 4 * $product->price,
        'taxes' => json_encode($product->taxes->toArray()),
    ]);
    $newQuantity = 5;

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $product->id,
        'quantity' => $newQuantity,
    ]))->assertStatus(200)
        ->assertJson([
            'ui_cart_id' => $uiCartId,
            'items' => $cart->items->toArray(),
    ]);

    $this->assertDatabaseHas('carts', [
        'ui_cart_id' => $uiCartId,
    ]);

    $this->assertDatabaseHas('cart_items', [
        'cart_id' => $cart->id,
        'title' => $product->title,
        'slug' => $product->slug,
        'purchasable_id' => $product->id,
        'purchasable_type' => Product::class,
        'price' => $product->price * 100,
        'quantity' => $newQuantity,
        'taxes' => json_encode($product->taxes->toArray()),
        'total' => $product->price * 100 * $newQuantity,
        'total_with_taxes' => ($product->priceWithTaxes() * $newQuantity) * 100,
    ]);

});

test('if out of stock, a product in the cart can not be added', function () {

    AppSettings::factory()->create([
        'stock_control_mode' => StockControlModes::STRICT->value
    ]);

    $product = Product::factory()->published()->create([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 20.00,
        'stock' => 0,
    ]);

    $uiCartId = fake()->uuid();
    Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
    ]);


    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $product->id,
        'quantity' => 2,
    ]))->assertStatus(422)
        ->assertJson([
            'error' => [
                'code' => 422,
                'message' => 'Product is out of stock',
            ]
    ]);
});


test('if out of stock, a product in the cart can not be updated', function () {

    AppSettings::factory()->create([
        'stock_control_mode' => StockControlModes::STRICT->value
    ]);

    $product = Product::factory()->published()->create([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 20.00,
        'stock' => 5,
    ]);

    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
    ]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'purchasable_id' => $product->id,
        'purchasable_type' => Product::class,
        'title' => $product->title,
        'slug' => $product->slug,
        'price' => $product->price,
        'quantity' => 4,
        'total' => 4 * $product->price
    ]);

    expect($cart->items)->toHaveCount(1);

    $newQuantity = 6;

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $product->id,
        'quantity' => $newQuantity,
    ]))->assertStatus(422)
        ->assertJson([
            'error' => [
                'code' => 422,
                'message' => 'Product is out of stock',
            ]
    ]);


});
