<?php

use App\Enums\DiscountCalculationModes;
use App\Enums\ProductStatus;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tax;

test('price should be saved as integer in the database', function () {

    ProductVariant::factory()->create([
        'price' => 10.99,
    ]);

    $this->assertDatabaseHas('product_variants', [
        'price' => 1099,
    ]);
});

test('getting price as decimal value', function () {
    $product = ProductVariant::factory()->create([
        'price' => 10.99,
    ]);

    $this->assertEquals('10.99', $product->price);
});

test('getting price in dollars', function () {
    $product = ProductVariant::factory()->create([
        'price' => 10.99,
    ]);

    $this->assertEquals('$10.99', $product->priceInDollars);
});

test('getting the parent product', function () {
    $product = Product::factory()->create();

    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
    ]);

    $this->assertEquals($product->id, $variant->product->id);
});

test('variant taxes are defined by the parent product taxes', function () {

    $tax = Tax::factory()->create([
        'name' => 'IVA',
        'percentage' => 15,
    ]);

    $product = Product::factory()->create();

    $product->taxes()->attach($tax->id);

    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
    ]);

    $this->assertEquals($variant->taxes->first()->id, $tax->id);
});

test('publishing a variant', function () {
    $product = Product::factory()->create();

    $variant = ProductVariant::factory()->draft()->create([
        'product_id' => $product->id,
    ]);

    $variant->publish();

    expect($variant->fresh()->status)->toBe(ProductStatus::ACTIVE);
    expect($variant->fresh()->published_at)->not()->toBeNull();
});

test('unpublishing a variant', function () {
    $product = Product::factory()->create();

    $variant = ProductVariant::factory()->published()->create([
        'product_id' => $product->id,
    ]);

    $variant->unpublish();

    expect($variant->fresh()->status)->toBe(ProductStatus::DRAFT);
    expect($variant->fresh()->published_at)->toBeNull();
});

test('archiving a variant', function () {
    $product = Product::factory()->create();

    $variant = ProductVariant::factory()->published()->create([
        'product_id' => $product->id,
    ]);

    $variant->archive();

    expect($variant->fresh()->status)->toBe(ProductStatus::ARCHIVED);
    expect($variant->fresh()->archived_at)->not()->toBeNull();
});

test('get published variants', function () {

    $product = Product::factory()->published()->create();
    ProductVariant::factory(3)->published()->create([
        'product_id' => $product->id,
    ]);

    ProductVariant::factory(2)->draft()->create([
        'product_id' => $product->id,
    ]);

    expect($product->variants()->published()->count())->toBe(3);
});

test('get draft variants', function () {

    $product = Product::factory()->published()->create();
    ProductVariant::factory(2)->draft()->create([
        'product_id' => $product->id,
    ]);

    ProductVariant::factory(1)->published()->create([
        'product_id' => $product->id,
    ]);

    expect($product->variants()->draft()->count())->toBe(2);
});

test('get archived variants', function () {

    $product = Product::factory()->published()->create();
    ProductVariant::factory(2)->archived()->create([
        'product_id' => $product->id,
    ]);

    ProductVariant::factory(2)->published()->create([
        'product_id' => $product->id,
    ]);

    expect($product->variants()->archived()->count())->toBe(2);
});

it('calculates variant price with product taxes', function () {
    $taxIVA = Tax::factory()->create([
        'name' => 'IVA',
        'percentage' => 15,
        'description' => 'IVA 15%',
    ]);

    $taxISD = Tax::factory()->create([
        'name' => 'ISD',
        'percentage' => 10,
        'description' => 'ISD 10%',
    ]);

    $product = Product::factory()->published()->create([
        'price' => 52.32,
    ]);

    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'price' => 55.00,
    ]);

    $product->taxes()->attach([$taxIVA->id, $taxISD->id]);

    expect($variant->priceWithTaxes())->toBe(55.00 * (1 + ($taxIVA->percentage / 100) + ($taxISD->percentage / 100)));
});

test('getting formatted vatiation', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'variation' => [
            'Color' => 'Red',
            'Size' => 'XL',
        ],
    ]);

    expect($variant->formatted_variation)->toBe('Color: Red, Size: XL');

});

// discounts

it('calculates discounted variant price with taxes', function () {
    setDiscountCalculationMode();

    $discountA = Discount::factory()->active()->create([
        'name' => 'Discount A',
        'percentage' => 20,
        'start_date' => now()->subDay(),
        'end_date' => now()->addDays(15),
    ]);

    $discountB = Discount::factory()->active()->create([
        'name' => 'Discount B',
        'percentage' => 10,
        'start_date' => now()->subDay(),
        'end_date' => now()->addDay(),
    ]);

    $taxIVA = Tax::factory()->create([
        'name' => 'IVA',
        'percentage' => 15,
        'description' => 'IVA 15%',
    ]);

    $taxISD = Tax::factory()->create([
        'name' => 'ISD',
        'percentage' => 10,
        'description' => 'ISD 10%',
    ]);

    $product = Product::factory()->published()->create([
        'price' => 52.32,
    ]);

    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'price' => 55.00,
    ]);

    $product->taxes()->attach([$taxIVA->id, $taxISD->id]);

    $variant->discounts()->attach([$discountA->id, $discountB->id]);

    expect($variant->discountedPriceWithTaxes())->toBe((5500 - (5500 * 0.20)) * (1 + ($taxIVA->percentage / 100) + ($taxISD->percentage / 100)) / 100);
});

it('calculates variant price with discount calulated from the highest discount applied', function () {

    setDiscountCalculationMode(DiscountCalculationModes::HIGHEST);

    $variant = ProductVariant::factory()->published()->create([
        'price' => 100.00,
    ]);

    $discountA = Discount::factory()->create([
        'name' => 'Discount A',
        'percentage' => 20,
        'start_date' => now()->subDay(),
        'end_date' => now()->addDays(15),
        'status' => 'active',
    ]);

    $discountB = Discount::factory()->create([
        'name' => 'Discount B',
        'percentage' => 10,
        'start_date' => now()->subDay(),
        'end_date' => now()->addDay(),
        'status' => 'active',
    ]);

    $variant->discounts()->attach([$discountA->id, $discountB->id]);

    expect($variant->discountedPrice())->toBe(80.00);
});

it('calculates variant price with discount calulated from the sum of discounts applied', function () {

    setDiscountCalculationMode(DiscountCalculationModes::SUM);

    $variant = ProductVariant::factory()->published()->create([
        'price' => 100.00,
    ]);

    $discountA = Discount::factory()->create([
        'name' => 'Discount A',
        'percentage' => 20,
        'start_date' => now()->subDay(),
        'end_date' => now()->addDays(15),
        'status' => 'active',
    ]);

    $discountB = Discount::factory()->create([
        'name' => 'Discount B',
        'percentage' => 10,
        'start_date' => now()->subDay(),
        'end_date' => now()->addDay(),
        'status' => 'active',
    ]);

    $discountC = Discount::factory()->create([
        'name' => 'Discount C',
        'percentage' => 20,
        'start_date' => now()->subDay(),
        'end_date' => now()->addDays(20),
        'status' => 'active',
    ]);

    $variant->discounts()->attach([$discountA->id, $discountB->id, $discountC->id]);

    expect($variant->discountedPrice())->toBe(50.00);
});

test('getting discounted price in dollars', function () {
    setDiscountCalculationMode(DiscountCalculationModes::SUM);

    $variant = ProductVariant::factory()->published()->create([
        'price' => 100.00,
    ]);

    $discountA = Discount::factory()->create([
        'name' => 'Discount A',
        'percentage' => 20,
        'start_date' => now()->subDay(),
        'end_date' => now()->addDays(15),
        'status' => 'active',
    ]);

    $discountB = Discount::factory()->create([
        'name' => 'Discount B',
        'percentage' => 10,
        'start_date' => now()->subDay(),
        'end_date' => now()->addDay(),
        'status' => 'active',
    ]);

    $variant->discounts()->attach([$discountA->id, $discountB->id]);

    expect($variant->discounted_price_in_dollars)->toBe('$70');
});
