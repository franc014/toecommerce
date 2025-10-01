<?php

use App\Models\Product;
use Inertia\Testing\AssertableInertia as Assert;

test('can show a list of published products', function () {

    $totalProducts = 3;
    $publishedProducts = Product::factory($totalProducts)->published()->create();

    $this->get(route('storefront.products'))->assertInertia(
        fn (Assert $page) => $page
            ->has('products', $totalProducts)
            ->has(
                'products.0',
                function (Assert $page) use ($publishedProducts) {
                    $page->where('id', $publishedProducts[0]->id)
                        ->where('title', $publishedProducts[0]->title)
                        ->where('slug', $publishedProducts[0]->slug)
                        ->where('price', $publishedProducts[0]->price)
                        ->where('price_in_dollars', $publishedProducts[0]->price_in_dollars)
                        ->where('images', $publishedProducts[0]->productImagesForList);
                }
            )
    );
});

test('can not show a list of unpublished products', function () {
    Product::factory(2)->draft()->create();
    $this->get(route('storefront.products'))->assertInertia(
        fn (Assert $page) => $page
            ->missing('products.0')
            ->missing('products.1')
    );
});
