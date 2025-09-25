<?php

use App\Models\Product;
use Inertia\Testing\AssertableInertia as Assert;

test('can show a list of published products', function () {

    $totalProducts = 3;
    $products = Product::factory($totalProducts)->published()->create();

    $this->get(route('storefront.products'))->assertInertia(
        fn (Assert $page) => $page
            ->has('products', $totalProducts)
            ->has(
                'products.0',
                function (Assert $page) use ($products) {
                    $page->where('id', $products[0]->id)
                        ->where('title', $products[0]->title)
                        ->where('slug', $products[0]->slug)
                        ->where('price', $products[0]->price)
                        ->where('price_in_dollars', $products[0]->price_in_dollars)
                        ->where('images', $products[0]->productImagesForList);

                }
            )
    );
});
