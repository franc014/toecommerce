<?php

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Purchasable;
use Illuminate\Contracts\Container\BindingResolutionException;
use App\Utils\ResolvesPurchasable;
use Illuminate\Support\Facades\Exceptions;

test('can resolve a purchasable product class', function () {

    $product = Product::factory()->published()->create();

    $resolver = new ResolvesPurchasable($product->id, 'product');

    $resolved = $resolver->resolve();

    expect($resolved)->toBeInstanceOf(Purchasable::class);
    expect($resolved->id)->toBe($product->id);

});

test('can resolve a purchasable product variant class', function () {

    $product = Product::factory()->published()->create();

    $variant = ProductVariant::factory()->published()->create([
        'product_id' => $product->id
    ]);

    $resolver = new ResolvesPurchasable($variant->id, 'product-variant');

    $resolved = $resolver->resolve();

    expect($resolved)->toBeInstanceOf(Purchasable::class);
    expect($resolved->id)->toBe($variant->id);

});

test('trying to resolve a purchasabe class that does not exist throws an exception', function () {

    Exceptions::fake();

    $resolver = new ResolvesPurchasable(1, 'any');

    $this->assertThrows(
        fn () => $resolver->resolve(),
        BindingResolutionException::class
    );

});
