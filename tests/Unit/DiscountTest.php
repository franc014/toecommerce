<?php

use App\Models\Discount;
use App\Models\Product;

test('a product can have many discounts', function () {
    $product = Product::factory()->create();
    $discount1 = Discount::factory()->create();
    $discount2 = Discount::factory()->create();
    $product->discounts()->attach([$discount1->id, $discount2->id]);
    expect($product->discounts)->toHaveCount(2);
});

test('fetching active discounts for a product', function () {
    $product = Product::factory()->create();
    $activeDiscount = Discount::factory()->create([
        'start_date' => now()->subDay(),
        'end_date' => now()->addDays(8),
    ]);
    $inactiveDiscount = Discount::factory()->create([
        'start_date' => now()->subDays(10),
        'end_date' => now()->subDays(5),
    ]);
    $product->discounts()->attach([$activeDiscount->id, $inactiveDiscount->id]);
    $activeDiscounts = $product->activeDiscounts();
    expect($activeDiscounts)->toHaveCount(1);
    expect($activeDiscounts->first()->id)->toBe($activeDiscount->id);
});
