<?php

use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

test('transforms cart item to array with all expected fields', function () {
    $cartItem = CartItem::factory()->create([
        'price' => 24.32,
        'quantity' => 2,
        'total' => 48.64,
    ]);

    $resource = CartItemResource::make($cartItem);
    $array = $resource->toArray(new Request);

    expect($array)->toHaveKeys([
        'id',
        'purchasable_id',
        'purchasable_type',
        'title',
        'image',
        'price_in_dollars',
        'total_in_dollars',
        'quantity',
        'image_url',
        'slug',
        'price',
        'variation',
        'taxes',
        'total',
        'total_with_taxes',
        'computed_taxes',
        'has_discount',
        'discount_percentage',
        'discounted_price',
        'total_with_taxes_in_dollars',
        'computed_taxes_in_dollars',
        'formatted_variation',
        'discounted_price_in_dollars',
    ]);
});

test('formats price and total in dollars correctly', function () {
    $cartItem = CartItem::factory()->create([
        'price' => 24.32,
        'quantity' => 2,
        'total' => 48.64,
    ]);

    $resource = CartItemResource::make($cartItem);
    $array = $resource->toArray(new Request);

    expect($array['price_in_dollars'])->toBe('$24.32')
        ->and($array['total_in_dollars'])->toBe('$48.64');
});

test('returns correct quantity as integer', function () {
    $cartItem = CartItem::factory()->create([
        'quantity' => 5,
    ]);

    $resource = CartItemResource::make($cartItem);
    $array = $resource->toArray(new Request);

    expect($array['quantity'])->toBe(5)
        ->and($array['quantity'])->toBeInt();
});

test('includes discount fields when discount is applied', function () {
    $cartItem = CartItem::factory()->create([
        'price' => 24.32,
        'has_discount' => true,
        'discount_percentage' => 20,
        'discounted_price' => 19.46,
    ]);

    $resource = CartItemResource::make($cartItem);
    $array = $resource->toArray(new Request);

    expect($array['has_discount'])->toBeTrue()
        ->and($array['discount_percentage'])->toBe(20)
        ->and($array['discounted_price'])->toBe(19.46)
        ->and($array['discounted_price_in_dollars'])->toBe('$19.46');
});

test('includes discount fields when no discount is applied', function () {
    $cartItem = CartItem::factory()->create([
        'price' => 24.32,
        'has_discount' => false,
        'discount_percentage' => 0,
        'discounted_price' => null,
    ]);

    $resource = CartItemResource::make($cartItem);
    $array = $resource->toArray(new Request);

    expect($array['has_discount'])->toBeFalse()
        ->and($array['discount_percentage'])->toBe(0)
        ->and($array['discounted_price'])->toBe(0.0)
        ->and($array['discounted_price_in_dollars'])->toContain('$0');
});

test('includes tax fields correctly when taxes are applied', function () {
    $taxes = [
        ['name' => 'IVA', 'percentage' => 15],
        ['name' => 'ISD', 'percentage' => 10],
    ];

    $cartItem = CartItem::factory()->create([
        'price' => 24.32,
        'quantity' => 2,
        'total' => 48.64,
        'taxes' => json_encode($taxes),
        'computed_taxes' => 12.16,
        'total_with_taxes' => 60.80,
    ]);

    $resource = CartItemResource::make($cartItem);
    $array = $resource->toArray(new Request);

    expect($array['taxes'])->toBeJson()
        ->and($array['computed_taxes'])->toBe(12.16)
        ->and($array['total_with_taxes'])->toBe(60.80)
        ->and($array['computed_taxes_in_dollars'])->toBe('$12.16')
        ->and($array['total_with_taxes_in_dollars'])->toContain('$60');
});

test('includes tax fields correctly when no taxes are applied', function () {
    $cartItem = CartItem::factory()->create([
        'price' => 24.32,
        'quantity' => 2,
        'total' => 48.64,
        'taxes' => json_encode([]),
        'computed_taxes' => 0,
        'total_with_taxes' => 48.64,
    ]);

    $resource = CartItemResource::make($cartItem);
    $array = $resource->toArray(new Request);

    expect($array['taxes'])->toBeJson()
        ->and($array['computed_taxes'])->toBe(0.0)
        ->and($array['total_with_taxes'])->toBe(48.64)
        ->and($array['computed_taxes_in_dollars'])->toContain('$0')
        ->and($array['total_with_taxes_in_dollars'])->toBe('$48.64');
});

test('formats variation correctly when variation exists', function () {
    $variation = [
        'Color' => 'Red',
        'Size' => 'XL',
    ];

    $cartItem = CartItem::factory()->create([
        'variation' => $variation,
    ]);

    $resource = CartItemResource::make($cartItem);
    $array = $resource->toArray(new Request);

    expect($array['variation'])->toBe($variation)
        ->and($array['formatted_variation'])->toBe('Color: Red, Size: XL');
});

test('formats variation correctly when variation is empty', function () {
    $cartItem = CartItem::factory()->create([
        'variation' => [],
    ]);

    $resource = CartItemResource::make($cartItem);
    $array = $resource->toArray(new Request);

    expect($array['variation'])->toBe([])
        ->and($array['formatted_variation'])->toBe('');
});

test('formats variation correctly when variation is null', function () {
    $cartItem = CartItem::factory()->create([
        'variation' => null,
    ]);

    $resource = CartItemResource::make($cartItem);
    $array = $resource->toArray(new Request);

    expect($array['variation'])->toBeNull()
        ->and($array['formatted_variation'])->toBe('');
});

test('includes image url from accessor', function () {
    $cartItem = CartItem::factory()->create([
        'image' => 'products/example.jpg',
    ]);

    $resource = CartItemResource::make($cartItem);
    $array = $resource->toArray(new Request);

    expect($array['image'])->toBe('products/example.jpg')
        ->and($array['image_url'])->toContain('products/example.jpg');
});

test('works with product as purchasable', function () {
    $product = Product::factory()->published()->create();
    $cartItem = CartItem::factory()->create([
        'purchasable_id' => $product->id,
        'purchasable_type' => Product::class,
    ]);

    $resource = CartItemResource::make($cartItem);
    $array = $resource->toArray(new Request);

    expect($array['purchasable_id'])->toBe($product->id)
        ->and($array['purchasable_type'])->toBe(Product::class);
});

test('works with product variant as purchasable', function () {
    $variant = ProductVariant::factory()->create();
    $cartItem = CartItem::factory()->create([
        'purchasable_id' => $variant->id,
        'purchasable_type' => ProductVariant::class,
    ]);

    $resource = CartItemResource::make($cartItem);
    $array = $resource->toArray(new Request);

    expect($array['purchasable_id'])->toBe($variant->id)
        ->and($array['purchasable_type'])->toBe(ProductVariant::class);
});

test('includes all monetary values as floats', function () {
    $cartItem = CartItem::factory()->create([
        'price' => 24.32,
        'total' => 48.64,
        'discounted_price' => 19.46,
        'computed_taxes' => 12.16,
        'total_with_taxes' => 60.80,
    ]);

    $resource = CartItemResource::make($cartItem);
    $array = $resource->toArray(new Request);

    expect($array['price'])->toBe(24.32)
        ->and($array['total'])->toBe(48.64)
        ->and($array['discounted_price'])->toBe(19.46)
        ->and($array['computed_taxes'])->toBe(12.16)
        ->and($array['total_with_taxes'])->toBe(60.80);
});
