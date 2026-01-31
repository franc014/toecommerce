<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tax;

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

test('can add a product to the cart after a product has been added', function () {

    $productA = Product::factory()->published()->create([
        'title' => 'Product A',
        'slug' => 'product-a',
        'price' => 20.00,
        'main_image' => 'image.jpg',
    ]);

    $productB = Product::factory()->published()->create([
        'title' => 'Product B',
        'slug' => 'product-b',
        'price' => 30.00,
        'main_image' => 'image.jpg',
    ]);

    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
    ]);

    $quantityToAdd = 2;

    expect($cart->hasItems())->toBeFalse();

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $productA->id,
        'quantity' => $quantityToAdd,
        'purchasable_type' => 'product',
    ]))->assertStatus(200);

    expect($cart->fresh()->items)->toHaveCount(1);
    expect($cart->fresh()->items_count)->toBe(2);

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $productB->id,
        'quantity' => $quantityToAdd,
        'purchasable_type' => 'product',
    ]))->assertStatus(200);

    expect($cart->fresh()->items)->toHaveCount(2);
    expect($cart->fresh()->items_count)->toBe(4);

    $this->assertDatabaseHas('cart_items', [
        'cart_id' => $cart->id,
        'title' => $productA->title,
        'slug' => $productA->slug,
        'image' => $productA->main_image,
        'purchasable_id' => $productA->id,
        'purchasable_type' => Product::class,
        'price' => $productA->price * 100,
        'quantity' => $quantityToAdd,
        'taxes' => json_encode($productA->taxes->toArray()),
        'total' => $productA->price * 100 * $quantityToAdd,
        'total_with_taxes' => ($productA->priceWithTaxes() * $quantityToAdd) * 100,
        'computed_taxes' => $productA->computedTaxes() * $quantityToAdd * 100,
    ]);

    $this->assertDatabaseHas('cart_items', [
        'cart_id' => $cart->id,
        'title' => $productB->title,
        'slug' => $productB->slug,
        'image' => $productB->main_image,
        'purchasable_id' => $productB->id,
        'purchasable_type' => Product::class,
        'price' => $productB->price * 100,
        'quantity' => $quantityToAdd,
        'taxes' => json_encode($productB->taxes->toArray()),
        'total' => $productB->price * 100 * $quantityToAdd,
        'total_with_taxes' => ($productB->priceWithTaxes() * $quantityToAdd) * 100,
        'computed_taxes' => $productB->computedTaxes() * $quantityToAdd * 100,
    ]);

});

test('can add a variant to the cart after a product has been added', function () {

    $this->withoutExceptionHandling();

    $product = Product::factory()->published()->create([
        'title' => 'Product A',
        'slug' => 'product-a',
        'price' => 20.00,
        'main_image' => 'image.jpg',
    ]);

    $variant = ProductVariant::factory()->published()->create([
        'price' => 10.00,
        'title' => 'Variant A',
        'slug' => 'variant-a',
        'main_image' => 'image.jpg',
    ]);

    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
    ]);

    $quantityToAdd = 2;

    expect($cart->hasItems())->toBeFalse();

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $product->id,
        'quantity' => $quantityToAdd,
        'purchasable_type' => 'product',
    ]))->assertStatus(200);

    expect($cart->fresh()->items)->toHaveCount(1);
    expect($cart->fresh()->items_count)->toBe(2);

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $variant->id,
        'quantity' => $quantityToAdd,
        'purchasable_type' => 'product-variant',
    ]))->assertStatus(200);

    expect($cart->fresh()->items)->toHaveCount(2);
    expect($cart->fresh()->items_count)->toBe(4);

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

    $this->assertDatabaseHas('cart_items', [
        'cart_id' => $cart->id,
        'title' => $variant->title,
        'slug' => $variant->slug,
        'image' => $variant->main_image,
        'purchasable_id' => $variant->id,
        'purchasable_type' => ProductVariant::class,
        'price' => $variant->price * 100,
        'quantity' => $quantityToAdd,
        'taxes' => json_encode($variant->taxes->toArray()),
        'total' => $variant->price * 100 * $quantityToAdd,
        'total_with_taxes' => ($variant->priceWithTaxes() * $quantityToAdd) * 100,
        'computed_taxes' => $variant->computedTaxes() * $quantityToAdd * 100,
    ]);

});

test('can add a product to the cart after a variant has been added', function () {

    $this->withoutExceptionHandling();

    $product = Product::factory()->published()->create([
        'title' => 'Product A',
        'slug' => 'product-a',
        'price' => 20.00,
        'main_image' => 'image.jpg',
    ]);

    $variant = ProductVariant::factory()->published()->create([
        'price' => 10.00,
        'title' => 'Variant A',
        'slug' => 'variant-a',
        'main_image' => 'image.jpg',
    ]);

    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
    ]);

    $quantityToAdd = 2;

    expect($cart->hasItems())->toBeFalse();

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $variant->id,
        'quantity' => $quantityToAdd,
        'purchasable_type' => 'product-variant',
    ]))->assertStatus(200);

    expect($cart->fresh()->items)->toHaveCount(1);
    expect($cart->fresh()->items_count)->toBe(2);

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $product->id,
        'quantity' => $quantityToAdd,
        'purchasable_type' => 'product',
    ]))->assertStatus(200);

    expect($cart->fresh()->items)->toHaveCount(2);
    expect($cart->fresh()->items_count)->toBe(4);

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

    $this->assertDatabaseHas('cart_items', [
        'cart_id' => $cart->id,
        'title' => $variant->title,
        'slug' => $variant->slug,
        'image' => $variant->main_image,
        'purchasable_id' => $variant->id,
        'purchasable_type' => ProductVariant::class,
        'price' => $variant->price * 100,
        'quantity' => $quantityToAdd,
        'taxes' => json_encode($variant->taxes->toArray()),
        'total' => $variant->price * 100 * $quantityToAdd,
        'total_with_taxes' => ($variant->priceWithTaxes() * $quantityToAdd) * 100,
        'computed_taxes' => $variant->computedTaxes() * $quantityToAdd * 100,
    ]);

});

test('can add a variant to the cart after a variant has been added', function () {

    $this->withoutExceptionHandling();

    $variantA = ProductVariant::factory()->published()->create([
        'price' => 10.00,
        'title' => 'Variant A',
        'slug' => 'variant-a',
        'main_image' => 'image.jpg',
    ]);

    $variantB = ProductVariant::factory()->published()->create([
        'price' => 10.00,
        'title' => 'Variant B',
        'slug' => 'variant-b',
        'main_image' => 'image.jpg',
    ]);

    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
    ]);

    $quantityToAdd = 2;

    expect($cart->hasItems())->toBeFalse();

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $variantA->id,
        'quantity' => $quantityToAdd,
        'purchasable_type' => 'product-variant',
    ]))->assertStatus(200);

    expect($cart->fresh()->items)->toHaveCount(1);
    expect($cart->fresh()->items_count)->toBe(2);

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $variantB->id,
        'quantity' => $quantityToAdd,
        'purchasable_type' => 'product-variant',
    ]))->assertStatus(200);

    expect($cart->fresh()->items)->toHaveCount(2);
    expect($cart->fresh()->items_count)->toBe(4);

    $this->assertDatabaseHas('cart_items', [
        'cart_id' => $cart->id,
        'title' => $variantA->title,
        'slug' => $variantA->slug,
        'image' => $variantA->main_image,
        'purchasable_id' => $variantA->id,
        'purchasable_type' => ProductVariant::class,
        'price' => $variantA->price * 100,
        'quantity' => $quantityToAdd,
        'taxes' => json_encode($variantA->taxes->toArray()),
        'total' => $variantA->price * 100 * $quantityToAdd,
        'total_with_taxes' => ($variantA->priceWithTaxes() * $quantityToAdd) * 100,
        'computed_taxes' => $variantA->computedTaxes() * $quantityToAdd * 100,
    ]);

    $this->assertDatabaseHas('cart_items', [
        'cart_id' => $cart->id,
        'title' => $variantB->title,
        'slug' => $variantB->slug,
        'image' => $variantB->main_image,
        'purchasable_id' => $variantB->id,
        'purchasable_type' => ProductVariant::class,
        'price' => $variantB->price * 100,
        'quantity' => $quantityToAdd,
        'taxes' => json_encode($variantB->taxes->toArray()),
        'total' => $variantB->price * 100 * $quantityToAdd,
        'total_with_taxes' => ($variantB->priceWithTaxes() * $quantityToAdd) * 100,
        'computed_taxes' => $variantB->computedTaxes() * $quantityToAdd * 100,
    ]);

});

test('can add a variant to the cart after its product has been added', function () {

    $product = Product::factory()->published()->create([
        'title' => 'Product A',
        'slug' => 'product-a',
        'price' => 20.00,
        'main_image' => 'image.jpg',
    ]);

    $variant = ProductVariant::factory()->published()->create([
        'product_id' => $product->id,
        'price' => 10.00,
        'title' => 'Variant B',
        'slug' => 'variant-b',
        'main_image' => 'image.jpg',
    ]);

    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
    ]);

    $quantityToAdd = 2;

    expect($cart->hasItems())->toBeFalse();

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $product->id,
        'quantity' => $quantityToAdd,
        'purchasable_type' => 'product',
    ]))->assertStatus(200);

    expect($cart->fresh()->items)->toHaveCount(1);
    expect($cart->fresh()->items_count)->toBe(2);

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $variant->id,
        'quantity' => $quantityToAdd,
        'purchasable_type' => 'product-variant',
    ]))->assertStatus(200);

    expect($cart->fresh()->items)->toHaveCount(2);
    expect($cart->fresh()->items_count)->toBe(4);

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

    $this->assertDatabaseHas('cart_items', [
        'cart_id' => $cart->id,
        'title' => $variant->title,
        'slug' => $variant->slug,
        'image' => $variant->main_image,
        'purchasable_id' => $variant->id,
        'purchasable_type' => ProductVariant::class,
        'price' => $variant->price * 100,
        'quantity' => $quantityToAdd,
        'taxes' => json_encode($variant->taxes->toArray()),
        'total' => $variant->price * 100 * $quantityToAdd,
        'total_with_taxes' => ($variant->priceWithTaxes() * $quantityToAdd) * 100,
        'computed_taxes' => $variant->computedTaxes() * $quantityToAdd * 100,
    ]);

});

test('can add a product to the cart after its variant has been added', function () {

    $product = Product::factory()->published()->create([
        'title' => 'Product A',
        'slug' => 'product-a',
        'price' => 20.00,
        'main_image' => 'image.jpg',
    ]);

    $variant = ProductVariant::factory()->published()->create([
        'product_id' => $product->id,
        'price' => 10.00,
        'title' => 'Variant B',
        'slug' => 'variant-b',
        'main_image' => 'image.jpg',
    ]);

    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
    ]);

    $quantityToAdd = 2;

    expect($cart->hasItems())->toBeFalse();

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $variant->id,
        'quantity' => $quantityToAdd,
        'purchasable_type' => 'product-variant',
    ]))->assertStatus(200);

    expect($cart->fresh()->items)->toHaveCount(1);
    expect($cart->fresh()->items_count)->toBe(2);

    $this->post(route('cart.items.addOrUpdate', [
        'ui_cart_id' => $uiCartId,
        'product_id' => $product->id,
        'quantity' => $quantityToAdd,
        'purchasable_type' => 'product',
    ]))->assertStatus(200);

    expect($cart->fresh()->items)->toHaveCount(2);
    expect($cart->fresh()->items_count)->toBe(4);

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

    $this->assertDatabaseHas('cart_items', [
        'cart_id' => $cart->id,
        'title' => $variant->title,
        'slug' => $variant->slug,
        'image' => $variant->main_image,
        'purchasable_id' => $variant->id,
        'purchasable_type' => ProductVariant::class,
        'price' => $variant->price * 100,
        'quantity' => $quantityToAdd,
        'taxes' => json_encode($variant->taxes->toArray()),
        'total' => $variant->price * 100 * $quantityToAdd,
        'total_with_taxes' => ($variant->priceWithTaxes() * $quantityToAdd) * 100,
        'computed_taxes' => $variant->computedTaxes() * $quantityToAdd * 100,
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

/**
 *  Discount tests
 * **/
test('can add a published product with discount to the cart', function () {

    setDiscountCalculationMode();

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

    $taxA = Tax::factory()->create([
        'name' => 'VAT',
        'percentage' => 15,
    ]);

    $taxB = Tax::factory()->create([
        'name' => 'Service Tax',
        'percentage' => 5,
    ]);

    $product->taxes()->attach([$taxA->id, $taxB->id]);

    $discountA = Discount::factory()->active()->create([
        'name' => 'Summer Sale',
        'percentage' => 10,
    ]);

    $discountB = Discount::factory()->active()->create([
        'name' => 'Another Sale',
        'percentage' => 20,
    ]);

    $product->discounts()->attach([$discountA->id, $discountB->id]);

    $discountedPrice = $product->price - ($product->price * 0.20);

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
        'has_discount' => 1,
        'discounted_price' => $discountedPrice * 100,
        // 'discount_percentage' => 20,
        'quantity' => $quantityToAdd,
        'total' => $discountedPrice * 100 * $quantityToAdd,
        'total_with_taxes' => (($discountedPrice + ($discountedPrice * 0.20)) * $quantityToAdd) * 100,
        'computed_taxes' => $discountedPrice * 0.20 * 100, // $product->computedTaxes() * $quantityToAdd * 100,
    ]);

});

test('can update an existing cart item quantity with discount', function () {
    setDiscountCalculationMode();
    $product = Product::factory()->published()->create([
        'title' => 'Product 1',
        'slug' => 'product-1',
        'price' => 20.00,
    ]);
    $discountA = Discount::factory()->active()->create([
        'name' => 'Summer Sale',
        'percentage' => 10,
    ]);
    $discountB = Discount::factory()->active()->create([
        'name' => 'Another Sale',
        'percentage' => 20,
    ]);

    $taxA = Tax::factory()->create([
        'name' => 'VAT',
        'percentage' => 15,
    ]);

    $taxB = Tax::factory()->create([
        'name' => 'Service Tax',
        'percentage' => 5,
    ]);

    $uiCartId = fake()->uuid();
    $cart = Cart::factory()->create([
        'ui_cart_id' => $uiCartId,
    ]);

    $product->discounts()->attach([$discountA->id, $discountB->id]);
    $product->taxes()->attach([$taxA->id, $taxB->id]);
    $discountedPrice = $product->price - ($product->price * 0.20);

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
        'has_discount' => true,
        'discounted_price' => $discountedPrice,
        'total_with_taxes' => (($discountedPrice + ($discountedPrice * 0.20)) * 4),
        'computed_taxes' => $discountedPrice * 0.20 * 4,
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
        'total' => $discountedPrice * 100 * $newQuantity,
        'total_with_taxes' => (($discountedPrice + ($discountedPrice * 0.20)) * $newQuantity) * 100,
        'computed_taxes' => $discountedPrice * 0.20 * $newQuantity * 100,
    ]);

});
