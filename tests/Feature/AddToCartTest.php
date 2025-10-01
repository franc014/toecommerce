<?php

use App\Enums\StockControlModes;
use App\Models\AppSettings;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;

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
    ]))->assertStatus(200);


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

test('can not add an unpublished product to the cart', function () {
    $product = Product::factory()->draft()->create([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 20.00,
    ]);
    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create([
        'ui_cart_id' => $uiCartId,
    ]);
    $quantityToAdd = 1;

    expect($cart->items)->toHaveCount(2);

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $product->id,
        'quantity' => $quantityToAdd,
    ]))->assertStatus(404);

    expect($cart->items)->toHaveCount(2);

});

//todo: can not add an unpublished product

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
    ]))->assertStatus(200);

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

//no cart validation: ui_cart_id, required, exists in carts table
test('can not add or update a cart item if the cart does not exist', function () {
    $product = Product::factory()->published()->create([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 20.00,
        'stock' => 5,
    ]);


    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => fake()->uuid(),
        'product_id' => $product->id,
        'quantity' => 2,
    ]))->assertStatus(404);
});

test('cart ui id is required', function () {
    $product = Product::factory()->published()->create([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 20.00,
        'stock' => 5,
    ]);

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => '',
        'product_id' => $product->id,
        'quantity' => 2,
    ]))->assertInvalid(['ui_cart_id']);

});

test('cart ui id should be a valid uuid', function () {

    $uiCartId = '7h15-1s-4n-1nv4l1d-u1d';
    Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
    ]);
    $product = Product::factory()->published()->create([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 20.00,
        'stock' => 5,
    ]);

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $product->id,
        'quantity' => 2,
    ]))->assertInvalid(['ui_cart_id']);

});

test('can not add or update a cart item if the product does not exist', function () {
    $uiCartId = fake()->uuid();
    Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
    ]);

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => 3,
        'quantity' => 2,
    ]))->assertStatus(404);

});

test('product id is required', function () {
    $uiCartId = fake()->uuid();
    Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
    ]);

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => '',
        'quantity' => 2,
    ]))->assertInvalid(['product_id']);

});


test('product id should be integer', function () {
    $uiCartId = fake()->uuid();
    Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
    ]);

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => 'uouou',
        'quantity' => 2,
    ]))->assertInvalid(['product_id']);

});

test('quantity is required', function () {
    $product = Product::factory()->published()->create([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 20.00,
        'stock' => 5,
    ]);

    $uiCartId = fake()->uuid();
    Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
    ]);

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $product->id,
        'quantity' => '',
    ]))->assertInvalid(['quantity']);
});

test('quantity should be integer', function () {
    $product = Product::factory()->published()->create([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 20.00,
        'stock' => 5,
    ]);

    $uiCartId = fake()->uuid();
    Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
    ]);

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $product->id,
        'quantity' => 'four',
    ]))->assertInvalid(['quantity']);
});
