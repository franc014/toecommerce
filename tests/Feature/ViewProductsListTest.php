<?php

use App\Models\Product;
use App\Models\ProductVariant;
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
                        ->where('images', $publishedProducts[0]->productImagesForList)
                        ->where('has_variants', $publishedProducts[0]->hasPublishedVariants())
                        ->where('variants', $publishedProducts[0]->variants);
                }
            )
    );
});

test('can show a list of published products with variants', function () {

    $totalProducts = 3;
    $publishedProducts = Product::factory($totalProducts)->published()->create();

    $productWithVariants = $publishedProducts[2];

    $variants = ProductVariant::factory(2)->published()->create([
        'product_id' => $productWithVariants->id,
    ]);

    $this->get(route('storefront.products'))->assertInertia(
        fn (Assert $page) => $page
            ->has('products', $totalProducts)
            ->has(
                'products.2',
                function (Assert $page) use ($publishedProducts, $variants) {
                    $page->where('id', $publishedProducts[2]->id)
                        ->where('title', $publishedProducts[2]->title)
                        ->where('slug', $publishedProducts[2]->slug)
                        ->where('price', $publishedProducts[2]->price)
                        ->where('price_in_dollars', $publishedProducts[2]->price_in_dollars)
                        ->where('images', $publishedProducts[2]->productImagesForList)
                        ->where('has_variants', $publishedProducts[2]->hasPublishedVariants())
                        ->where('variants', $publishedProducts[2]->variants);
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
