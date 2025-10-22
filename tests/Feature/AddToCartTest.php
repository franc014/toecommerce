<?php

use App\Enums\StockControlModes;
use App\Models\AppSettings;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;

it('defines the product data to add to the cart', function () {

    $product = Product::factory()->published()->create([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 20.00,
        'main_image' => 'image.jpg',
    ]);

    $data = $product->dataforCart();

    expect($data)->toEqual([
        'purchasable_id' => $product->id,
        'title' => $product->title,
        'price' => $product->price,
        'slug' => $product->slug,
        'taxes' => json_encode($product->taxes->toArray()),
        'purchasable_type' => Product::class,
        'image' => 'image.jpg',
    ]);

});

it('defines the product variant data to add to the cart', function () {

    $variant = ProductVariant::factory()->published()->create([
        'title' => 'Variant 1',
        'slug' => 'variant-1',
        'price' => 20.00,
        'main_image' => 'image.jpg',
        'variation' => [
            'color' => 'red',
            'size' => 'L',
        ],
    ]);

    $data = $variant->dataforCart();

    expect($data)->toEqual([
        'purchasable_id' => $variant->id,
        'purchasable_type' => ProductVariant::class,
        'title' => $variant->title,
        'price' => $variant->price,
        'slug' => $variant->slug,
        'image' => $variant->main_image,
        'taxes' => $variant->taxes,
        'variation' => $variant->variation,
    ]);

});

it('if no variant image is set it will use the product image', function () {

    $variant = ProductVariant::factory()->published()->create([
        'title' => 'Variant 1',
        'slug' => 'variant-1',
        'price' => 20.00,
        'main_image' => null,
    ]);

    $data = $variant->dataforCart();

    expect($data)->toEqual([
        'purchasable_id' => $variant->id,
        'purchasable_type' => ProductVariant::class,
        'title' => $variant->title,
        'price' => $variant->price,
        'slug' => $variant->slug,
        'image' => $variant->product->main_image,
        'taxes' => json_encode([]),
        'variation' => $variant->variation,
    ]);
});

test('can add a published product to the cart', function () {

    $product = Product::factory()->published()->create([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 20.00,
        'main_image' => 'image.jpg',
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
        'purchasable_type' => 'product',
    ]))->assertStatus(200);

    expect($cart->fresh()->items)->toHaveCount(3);

    $this->assertDatabaseHas('carts', [
        'ui_cart_id' => $uiCartId,
    ]);

    $this->assertDatabaseHas('cart_items', [
        'cart_id' => $cart->id,
        'title' => $product->title,
        'slug' => $product->slug,
        'image' => $product->main_image,
        'purchasable_id' => $product->id,
        'purchasable_type' => Product::class,
        'price' => $product->price * 100,
        'quantity' => $quantityToAdd,
        'taxes' => json_encode($product->taxes->toArray()),
        'total' => $product->price * 100 * $quantityToAdd,
        'total_with_taxes' => ($product->priceWithTaxes() * $quantityToAdd) * 100,
        'computed_taxes' => $product->computedTaxes() * $quantityToAdd * 100,
    ]);

});

test('can add a published product variant to the cart', function () {

    $product = Product::factory()->published()->create([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 20.00,
        'main_image' => 'image.jpg',
    ]);

    $variant = ProductVariant::factory()->published()->create([
        'product_id' => $product->id,
        'price' => 10.00,
        'title' => 'Variant 1',
        'slug' => 'variant-1',
        'main_image' => $product->main_image,
        'variation' => [
            'color' => 'red',
            'size' => 'L',
        ],
    ]);

    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create([
        'ui_cart_id' => $uiCartId,
    ]);
    $quantityToAdd = 1;

    expect($cart->items)->toHaveCount(2);

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $variant->id,
        'quantity' => $quantityToAdd,
        'purchasable_type' => 'product-variant',
    ]))->assertStatus(200);

    $this->assertDatabaseHas('carts', [
        'ui_cart_id' => $uiCartId,
    ]);

    $this->assertDatabaseHas('cart_items', [
        'cart_id' => $cart->id,
        'title' => $variant->title,
        'slug' => $variant->slug,
        'image' => $variant->main_image,
        'purchasable_id' => $variant->id,
        'purchasable_type' => ProductVariant::class,
        'price' => $variant->price * 100,
        'quantity' => $quantityToAdd,
        'taxes' => json_encode($product->taxes->toArray()),
        'total' => $variant->price * 100 * $quantityToAdd,
        'total_with_taxes' => ($variant->priceWithTaxes() * $quantityToAdd) * 100,
        'computed_taxes' => $variant->computedTaxes() * $quantityToAdd * 100,
        'variation' => json_encode($variant->variation),
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
        'purchasable_type' => 'product',
    ]))->assertStatus(404);

    expect($cart->items)->toHaveCount(2);

});

test('can not add a unpublished product variant to the cart', function () {

    $product = Product::factory()->published()->create([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 20.00,
    ]);

    $variant = ProductVariant::factory()->draft()->create([
        'product_id' => $product->id,
        'price' => 10.00,
        'title' => 'Variant 1',
        'slug' => 'variant-1',
    ]);

    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create([
        'ui_cart_id' => $uiCartId,
    ]);
    $quantityToAdd = 1;

    expect($cart->items)->toHaveCount(2);

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $variant->id,
        'quantity' => $quantityToAdd,
        'purchasable_type' => 'product-variant',
    ]))->assertStatus(404);

    expect($cart->items)->toHaveCount(2);

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
        'purchasable_type' => 'product',
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
        'computed_taxes' => $product->computedTaxes() * $newQuantity * 100,
    ]);

});

test('if out of stock, a product in the cart can not be added', function () {

    AppSettings::factory()->create([
        'stock_control_mode' => StockControlModes::STRICT->value,
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
        'purchasable_type' => 'product',
    ]))->assertStatus(422)
        ->assertJson([
            'error' => [
                'code' => 422,
                'message' => 'Product is out of stock',
            ],
        ]);
});

test('if out of stock, a variant in the cart can not be added', function () {

    AppSettings::factory()->create([
        'stock_control_mode' => StockControlModes::STRICT->value,
    ]);

    $product = Product::factory()->published()->create([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 20.00,
        'stock' => 10,
    ]);

    $variant = ProductVariant::factory()->published()->create([
        'product_id' => $product->id,
        'stock' => 0,
    ]);

    $uiCartId = fake()->uuid();
    Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
    ]);

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $variant->id,
        'quantity' => 2,
        'purchasable_type' => 'product-variant',
    ]))->assertStatus(422)
        ->assertJson([
            'error' => [
                'code' => 422,
                'message' => 'Product is out of stock',
            ],
        ]);
});

test('if out of stock, a product in the cart can not be updated', function () {

    AppSettings::factory()->create([
        'stock_control_mode' => StockControlModes::STRICT->value,
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
        'total' => 4 * $product->price,
    ]);

    expect($cart->items)->toHaveCount(1);

    $newQuantity = 6;

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $product->id,
        'quantity' => $newQuantity,
        'purchasable_type' => 'product',
    ]))->assertStatus(422)
        ->assertJson([
            'error' => [
                'code' => 422,
                'message' => 'Product is out of stock',
            ],
        ]);
});

test('if out of stock, a variant in the cart can not be updated', function () {

    AppSettings::factory()->create([
        'stock_control_mode' => StockControlModes::STRICT->value,
    ]);

    $product = Product::factory()->published()->create([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 20.00,
        'stock' => 7,
    ]);

    $variant = ProductVariant::factory()->published()->create([
        'product_id' => $product->id,
        'stock' => 5,
    ]);

    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
    ]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'purchasable_id' => $variant->id,
        'purchasable_type' => ProductVariant::class,
        'title' => $variant->title,
        'slug' => $variant->slug,
        'price' => $variant->price,
        'quantity' => 4,
        'total' => 4 * $variant->price,
    ]);

    expect($cart->items)->toHaveCount(1);

    $newQuantity = 6;

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $variant->id,
        'quantity' => $newQuantity,
        'purchasable_type' => 'product-variant',
    ]))->assertStatus(422)
        ->assertJson([
            'error' => [
                'code' => 422,
                'message' => 'Product is out of stock',
            ],
        ]);
});

// no cart validation: ui_cart_id, required, exists in carts table
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
        'purchasable_type' => 'product',
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
        'purchasable_type' => 'product',
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
        'purchasable_type' => 'product',
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
        'purchasable_type' => 'product',
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
        'purchasable_type' => 'product',
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
        'purchasable_type' => 'product',
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
        'purchasable_type' => 'product',
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
        'purchasable_type' => 'product',
    ]))->assertInvalid(['quantity']);

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $product->id,
        'quantity' => 2.2,
        'purchasable_type' => 'product',
    ]))->assertInvalid(['quantity']);
});

test('quantity should not be less than 1', function () {
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
        'quantity' => 0,
        'purchasable_type' => 'product',
    ]))->assertInvalid(['quantity']);

});

test('quantity should not be greater than stock', function () {
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
        'quantity' => 7,
        'purchasable_type' => 'product',
    ]))->assertInvalid(['quantity']);

});

test('purchasable type is required', function () {
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
        'quantity' => 4,
    ]))->assertInvalid(['purchasable_type']);
});
