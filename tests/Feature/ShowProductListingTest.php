<?php

use App\Models\Product;
use Inertia\Testing\AssertableInertia as Assert;

it('shows a listing of a published product', function () {
    $this->withoutExceptionHandling();

    $product = Product::factory()->published()->create();
    $this->get(route('storefront.product', ['product' => $product->slug]))
        ->assertInertia(function (Assert $page) use ($product) {
            return $page->has('product', function (Assert $page) use ($product) {
                return $page
                    ->where('id', $product->id)
                    ->where('title', $product->title)
                    ->where('description', $product->description)
                    ->where('slug', $product->slug)
                    ->where('price_in_dollars', $product->price_in_dollars)
                    ->where('taxes', $product->taxes)
                    ->where('price_with_taxes_in_dollars', $product->price_with_taxes_in_dollars)
                    ->where('images', $product->productImagesForList);
            });
        });

});
