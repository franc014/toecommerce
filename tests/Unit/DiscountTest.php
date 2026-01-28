<?php

use App\Enums\DiscountStatus;
use App\Models\Discount;
use App\Models\Product;

test('a product can have many discounts', function () {
    $product = Product::factory()->create();
    $discount1 = Discount::factory()->create();
    $discount2 = Discount::factory()->create();
    $product->discounts()->attach([$discount1->id, $discount2->id]);
    expect($product->discounts)->toHaveCount(2);
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

    expect($discount->fresh()->status)->toBe(DiscountStatus::INACTIVE->value);
});

test('fetching valid discounts available', function () {

    $activeDiscount = Discount::factory()->create([
        'start_date' => now()->subDay(),
        'end_date' => now()->addDays(8),
        'status' => DiscountStatus::ACTIVE->value,
    ]);
    $inactiveDiscount = Discount::factory()->create([
        'start_date' => now()->subDays(10),
        'end_date' => now()->subDays(5),
        'status' => DiscountStatus::INACTIVE->value,
    ]);

    $scheduledDiscount = Discount::factory()->create([
        'start_date' => now()->addDays(2),
        'end_date' => now()->addDays(10),
        'status' => DiscountStatus::SCHEDULED->value,
    ]);

    $validDiscounts = Discount::valid()->get();
    expect($validDiscounts)->toHaveCount(2);

});

test('fetching valid discounts for a product', function () {
    $product = Product::factory()->create();
    $activeDiscount = Discount::factory()->create([
        'start_date' => now()->subDay(),
        'end_date' => now()->addDays(8),
        'status' => DiscountStatus::ACTIVE->value,
    ]);
    $scheduledDiscount = Discount::factory()->create([
        'start_date' => now()->addDays(2),
        'end_date' => now()->addDays(10),
        'status' => DiscountStatus::SCHEDULED->value,
    ]);
    $inactiveDiscount = Discount::factory()->create([
        'start_date' => now()->subDays(10),
        'end_date' => now()->subDays(5),
        'status' => DiscountStatus::INACTIVE->value,
    ]);
    $product->discounts()->attach([$activeDiscount->id, $inactiveDiscount->id, $scheduledDiscount->id]);
    $validDiscounts = $product->validDiscounts();

    expect($validDiscounts)->toHaveCount(2);

});
