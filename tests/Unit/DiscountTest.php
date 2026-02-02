<?php

use App\Enums\DiscountStatus;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductVariant;

test('a product can have many discounts', function () {
    $product = Product::factory()->create();
    $discount1 = Discount::factory()->create();
    $discount2 = Discount::factory()->create();
    $product->discounts()->attach([$discount1->id, $discount2->id]);
    expect($product->discounts)->toHaveCount(2);
});

test('a variant can have many discounts', function () {
    $variant = ProductVariant::factory()->create();
    $discount1 = Discount::factory()->create();
    $discount2 = Discount::factory()->create();
    $variant->discounts()->attach([$discount1->id, $discount2->id]);
    expect($variant->discounts)->toHaveCount(2);
});

test('setting status discounts', function () {

    $activeDiscount = Discount::factory()->create([
        'start_date' => now()->subDay(),
        'end_date' => now()->addDays(8),
    ]);

    $activeDiscount2 = Discount::factory()->create([
        'start_date' => now()->subDays(2),
        'end_date' => now()->addDays(5),
    ]);

    $inactiveDiscount = Discount::factory()->create([
        'start_date' => now()->subDays(10),
        'end_date' => now()->subDays(5),
    ]);

    $scheduledDiscount = Discount::factory()->create([
        'start_date' => now()->addDays(5),
        'end_date' => now()->addDays(10),
    ]);

    Discount::setStatus();

    $activeDiscounts = Discount::getByStatus(DiscountStatus::ACTIVE);
    $inactiveDiscounts = Discount::getByStatus(DiscountStatus::INACTIVE);
    $scheduledDiscounts = Discount::getByStatus(DiscountStatus::SCHEDULED);
    expect($inactiveDiscounts)->toHaveCount(1);
    expect($scheduledDiscounts)->toHaveCount(1);
    expect($activeDiscounts)->toHaveCount(2);

});

test('can change discount status manually', function () {
    $discount = Discount::factory()->create([
        'start_date' => now()->addDays(2),
        'end_date' => now()->addDays(8),
        'status' => DiscountStatus::SCHEDULED->value,
    ]);

    $discount->changeStatus(DiscountStatus::INACTIVE);

    expect($discount->fresh()->status)->toBe(DiscountStatus::INACTIVE);
});

test('fetching valid discounts available', function () {

    Discount::factory()->active()->create([
        'start_date' => now()->subDay(),
        'end_date' => now()->addDays(8),
    ]);
    Discount::factory()->inactive()->create([
        'start_date' => now()->subDays(10),
        'end_date' => now()->subDays(5),
    ]);

    Discount::factory()->scheduled()->create([
        'start_date' => now()->addDays(2),
        'end_date' => now()->addDays(10),
    ]);

    $validDiscounts = Discount::valid()->get();
    expect($validDiscounts)->toHaveCount(1);

});

test('fetching valid discounts for a product', function () {
    $product = Product::factory()->create();
    $activeDiscount = Discount::factory()->active()->create([
        'start_date' => now()->subDay(),
        'end_date' => now()->addDays(8),
    ]);
    $scheduledDiscount = Discount::factory()->scheduled()->create([
        'start_date' => now()->addDays(2),
        'end_date' => now()->addDays(10),
    ]);
    $inactiveDiscount = Discount::factory()->inactive()->create([
        'start_date' => now()->subDays(10),
        'end_date' => now()->subDays(5),
    ]);
    $product->discounts()->attach([$activeDiscount->id, $inactiveDiscount->id, $scheduledDiscount->id]);
    $validDiscounts = $product->validDiscounts();

    expect($validDiscounts)->toHaveCount(1);
});

test('fetching valid discounts for a variant', function () {
    $variant = ProductVariant::factory()->create();
    $activeDiscount = Discount::factory()->active()->create([
        'start_date' => now()->subDay(),
        'end_date' => now()->addDays(8),
    ]);
    $scheduledDiscount = Discount::factory()->scheduled()->create([
        'start_date' => now()->addDays(2),
        'end_date' => now()->addDays(10),
    ]);
    $inactiveDiscount = Discount::factory()->inactive()->create([
        'start_date' => now()->subDays(10),
        'end_date' => now()->subDays(5),
    ]);
    $variant->discounts()->attach([$activeDiscount->id, $inactiveDiscount->id, $scheduledDiscount->id]);
    $validDiscounts = $variant->validDiscounts();

    expect($validDiscounts)->toHaveCount(1);
});
