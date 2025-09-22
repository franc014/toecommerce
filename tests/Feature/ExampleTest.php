<?php

use App\Models\ProductVariant;

test('check product variant sizes', function () {
    $productVariant = ProductVariant::factory()->count(2)->create();

    ray($productVariant[0]->sizes);

    $this->assertCount(2, $productVariant->sizes);
});
