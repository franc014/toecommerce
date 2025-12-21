<?php

use App\Enums\StockControlModes;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Settings\StorefrontSettings;
use Inertia\Testing\AssertableInertia as Assert;

test('can show a list of published products', function () {
    setPaginationNumber(4);
    $totalProducts = 3;
    $publishedProducts = Product::factory($totalProducts)->published()->create([
        'price' => 10,
    ]);

    $this->get(route('storefront.products'))->assertInertia(
        fn (Assert $page) => $page
            ->has('products.data', $totalProducts)
            ->has(
                'products.data.0',
                function (Assert $page) use ($publishedProducts) {
                    $page->where('id', $publishedProducts[0]->id)
                        ->where('title', $publishedProducts[0]->title)
                        ->where('slug', $publishedProducts[0]->slug)
                        ->where('price', 10)
                        ->where('price_in_dollars', $publishedProducts[0]->price_in_dollars)
                        ->where('images', $publishedProducts[0]->productImagesForList)
                        ->where('video', $publishedProducts[0]->video)
                        ->where('has_variants', $publishedProducts[0]->hasPublishedVariants())
                        ->where('variants', $publishedProducts[0]->variants)
                        ->where('dropping_stock', false);
                }
            )
    );
});

test('can show a list of published products with variants', function () {
    setPaginationNumber(4);

    $totalProducts = 3;
    $publishedProducts = Product::factory($totalProducts)->published()->create([
        'price' => 20.5,
    ]);

    $productWithVariants = $publishedProducts[2];

    ProductVariant::factory(2)->published()->create([
        'product_id' => $productWithVariants->id,
    ]);

    $this->get(route('storefront.products'))->assertInertia(
        fn (Assert $page) => $page
            ->has('products.data', $totalProducts)
            ->has(
                'products.data.2',
                function (Assert $page) use ($publishedProducts) {
                    $page->where('id', $publishedProducts[2]->id)
                        ->where('title', $publishedProducts[2]->title)
                        ->where('slug', $publishedProducts[2]->slug)
                        ->where('price', 20.5)
                        ->where('price_in_dollars', $publishedProducts[2]->price_in_dollars)
                        ->where('images', $publishedProducts[2]->productImagesForList)
                        ->where('video', $publishedProducts[2]->video)
                        ->where('has_variants', $publishedProducts[2]->hasPublishedVariants())
                        ->where('variants', $publishedProducts[2]->variants)
                        ->where('dropping_stock', false);
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

it('shows warning text if product stock is dropping below threshold, in strict mode', function () {

    setStrictMode();
    setPaginationNumber(2);

    Product::factory()->published()->create();
    $productDropping = Product::factory()->published()->create([
        'title' => 'Product 1',
        'price' => 20,
        'stock_threshold_for_customers' => 10,
        'stock' => 8,
        'video' => 'a video url',
    ]);

    $sfSettings = app(StorefrontSettings::class);

    $this->get(route('storefront.products'))->assertInertia(
        fn (Assert $page) => $page
            ->has('products.data', 2)
            ->has(
                'products.data.1',
                function (Assert $page) use ($productDropping) {
                    $page->where('id', $productDropping->id)
                        ->where('title', $productDropping->title)
                        ->where('slug', $productDropping->slug)
                        ->where('price', 20)
                        ->where('price_in_dollars', $productDropping->price_in_dollars)
                        ->where('images', $productDropping->productImagesForList)
                        ->where('video', $productDropping->video)
                        ->where('has_variants', $productDropping->hasPublishedVariants())
                        ->where('variants', $productDropping->variants)
                        ->where('dropping_stock', true);
                }
            )
    );

});

it('does not show warning text if product stock is not dropping below threshold, in strict mode', function () {

    setStrictMode();
    setPaginationNumber(2);

    Product::factory()->published()->create();
    $productDropping = Product::factory()->published()->create([
        'title' => 'Product 1',
        'stock_threshold_for_customers' => 10,
        'stock' => 12,
    ]);

    $this->get(route('storefront.products'))->assertInertia(
        fn (Assert $page) => $page
            ->has('products.data', 2)
            ->has(
                'products.data.1',
                function (Assert $page) use ($productDropping) {
                    $page->where('id', $productDropping->id)
                        ->where('title', $productDropping->title)
                        ->where('slug', $productDropping->slug)
                        ->where('price', $productDropping->price)
                        ->where('price_in_dollars', $productDropping->price_in_dollars)
                        ->where('images', $productDropping->productImagesForList)
                        ->where('video', $productDropping->video)
                        ->where('has_variants', $productDropping->hasPublishedVariants())
                        ->where('variants', $productDropping->variants)
                        ->where('dropping_stock', false);
                }
            )
    );

});

it('does not show warning text if product stock is dropping below threshold, in nonstrict mode', function () {

    setStrictMode(StockControlModes::NONE);
    setPaginationNumber(2);

    Product::factory()->published()->create();
    $productDropping = Product::factory()->published()->create([
        'title' => 'Product 1',
        'stock_threshold_for_customers' => 10,
        'stock' => 8,
    ]);

    $this->get(route('storefront.products'))->assertInertia(
        fn (Assert $page) => $page
            ->has('products.data', 2)
            ->has(
                'products.data.1',
                function (Assert $page) use ($productDropping) {
                    $page->where('id', $productDropping->id)
                        ->where('title', $productDropping->title)
                        ->where('slug', $productDropping->slug)
                        ->where('price', $productDropping->price)
                        ->where('video', $productDropping->video)
                        ->where('price_in_dollars', $productDropping->price_in_dollars)
                        ->where('images', $productDropping->productImagesForList)
                        ->where('has_variants', $productDropping->hasPublishedVariants())
                        ->where('variants', $productDropping->variants)
                        ->where('dropping_stock', false);
                }
            )
    );

});
