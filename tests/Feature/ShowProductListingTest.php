<?php

use App\Enums\StockControlModes;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\HeroBlock;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductCollection;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

it('shows a listing of a published product', function () {

    $product = Product::factory()->published()->create([
        'main_image' => 'product.jpg',
    ]);

    $this->get(route('storefront.product', ['product' => $product->slug]))
        ->assertInertia(function (Assert $page) use ($product) {
            return $page->has('product', function (Assert $page) use ($product) {
                return $page
                    ->where('id', $product->id)
                    ->where('title', $product->title)
                    ->where('summary', $product->summary)
                    ->where('description', RichContentRenderer::make($product->description)->customBlocks([
                        HeroBlock::class,
                    ])->toHtml())
                    ->where('slug', $product->slug)
                    ->where('price_in_dollars', $product->price_in_dollars)
                    ->where('has_taxes', $product->hasTaxes())
                    ->where('taxes', $product->formatted_taxes)
                    ->where('price_with_taxes_in_dollars', $product->price_with_taxes_in_dollars)
                    ->where('has_variants', $product->hasVariants())
                    ->where('variants', $product->variants)
                    ->where('main_image', Storage::url($product->main_image))
                    ->where('images', $product->productImagesForList)
                    ->where('dropping_stock', false)
                    ->where('has_discounts', false)
                    ->where('discounted_price_in_dollars', '$0')
                    ->where('discounts', []);
            });
        });

});

it('shows warning text if product stock is dropping below threshold, in strict mode', function () {

    setStrictMode();

    $product = Product::factory()->published()->create([
        'main_image' => 'product.jpg',
        'stock_threshold_for_customers' => 10,
        'stock' => 8,
    ]);

    $this->get(route('storefront.product', ['product' => $product->slug]))
        ->assertInertia(function (Assert $page) use ($product) {
            return $page->has('product', function (Assert $page) use ($product) {
                return $page
                    ->where('id', $product->id)
                    ->where('title', $product->title)
                    ->where('description', RichContentRenderer::make($product->description)->customBlocks([
                        HeroBlock::class,
                    ])->toHtml())
                    ->where('slug', $product->slug)
                    ->where('summary', $product->summary)
                    ->where('price_in_dollars', $product->price_in_dollars)
                    ->where('has_taxes', $product->hasTaxes())
                    ->where('taxes', $product->formatted_taxes)
                    ->where('price_with_taxes_in_dollars', $product->price_with_taxes_in_dollars)
                    ->where('has_variants', $product->hasVariants())
                    ->where('variants', $product->variants)
                    ->where('main_image', Storage::url($product->main_image))
                    ->where('images', $product->productImagesForList)
                    ->where('dropping_stock', true)
                    ->where('has_discounts', false)
                    ->where('discounted_price_in_dollars', '$0')
                    ->where('discounts', []);
            });
        });

});

it('does not show warning text if product stock is not dropping below threshold, in strict mode', function () {

    setStrictMode();

    $product = Product::factory()->published()->create([
        'title' => 'Product 1',
        'stock_threshold_for_customers' => 10,
        'stock' => 12,
    ]);

    $this->get(route('storefront.product', ['product' => $product->slug]))
        ->assertInertia(function (Assert $page) use ($product) {
            return $page->has('product', function (Assert $page) use ($product) {
                return $page
                    ->where('id', $product->id)
                    ->where('title', $product->title)
                    ->where('description', RichContentRenderer::make($product->description)->customBlocks([
                        HeroBlock::class,
                    ])->toHtml())
                    ->where('slug', $product->slug)
                    ->where('summary', $product->summary)
                    ->where('price_in_dollars', $product->price_in_dollars)
                    ->where('has_taxes', $product->hasTaxes())
                    ->where('taxes', $product->formatted_taxes)
                    ->where('price_with_taxes_in_dollars', $product->price_with_taxes_in_dollars)
                    ->where('has_variants', $product->hasVariants())
                    ->where('variants', $product->variants)
                    ->where('main_image', Storage::url($product->main_image))
                    ->where('images', $product->productImagesForList)
                    ->where('dropping_stock', false)
                    ->where('has_discounts', false)
                    ->where('discounted_price_in_dollars', '$0')
                    ->where('discounts', []);
            });
        });

});

it('does not show warning text if product stock is dropping below threshold, in nonstrict mode', function () {

    setStrictMode(StockControlModes::NONE);

    $product = Product::factory()->published()->create([
        'title' => 'Product 1',
        'stock_threshold_for_customers' => 10,
        'stock' => 8,
    ]);

    $this->get(route('storefront.product', ['product' => $product->slug]))
        ->assertInertia(function (Assert $page) use ($product) {
            return $page->has('product', function (Assert $page) use ($product) {
                return $page
                    ->where('id', $product->id)
                    ->where('title', $product->title)
                    ->where('description', RichContentRenderer::make($product->description)->customBlocks([
                        HeroBlock::class,
                    ])->toHtml())
                    ->where('slug', $product->slug)
                    ->where('summary', $product->summary)
                    ->where('price_in_dollars', $product->price_in_dollars)
                    ->where('has_taxes', $product->hasTaxes())
                    ->where('taxes', $product->formatted_taxes)
                    ->where('price_with_taxes_in_dollars', $product->price_with_taxes_in_dollars)
                    ->where('has_variants', $product->hasVariants())
                    ->where('variants', $product->variants)
                    ->where('main_image', Storage::url($product->main_image))
                    ->where('images', $product->productImagesForList)
                    ->where('dropping_stock', false)
                    ->where('has_discounts', false)
                    ->where('discounted_price_in_dollars', '$0')
                    ->where('discounts', []);
            });
        });

});

test('can show a list of published related products based on collections', function () {

    $collectionA = ProductCollection::factory()->create([
        'title' => 'Collection A',
        'slug' => 'collection-a',
    ]);
    $collectionB = ProductCollection::factory()->create([
        'title' => 'Collection B',
        'slug' => 'collection-b',
    ]);

    $collectionC = ProductCollection::factory()->create([
        'title' => 'Collection C',
        'slug' => 'collection-c',
    ]);

    $productA = Product::factory()->published()->create([
        'title' => 'Product A',
        'slug' => 'product-a',
    ]);

    $productB = Product::factory()->published()->create([
        'title' => 'Product B',
        'slug' => 'product-b',
    ]);

    $productC = Product::factory()->published()->create([
        'title' => 'Product C',
        'slug' => 'product-c',
    ]);

    $productD = Product::factory()->published()->create([
        'title' => 'Product D',
        'slug' => 'product-d',
    ]);

    $productA->productCollections()->attach([$collectionA->id, $collectionB->id]);
    $productB->productCollections()->attach($collectionA);
    $productC->productCollections()->attach([$collectionB->id, $collectionC->id]);
    $productD->productCollections()->attach($collectionC);

    $this->get(route('storefront.product', ['product' => $productA->slug]))
        ->assertInertia(function (Assert $page) use ($productB) {
            return $page->has('relatedProducts', 2)
                ->has('relatedProducts.0', function (Assert $page) use ($productB) {
                    $page->where('id', $productB->id)
                        ->where('title', $productB->title)
                        ->where('slug', $productB->slug)
                        ->where('price', $productB->price)
                        ->where('price_in_dollars', $productB->price_in_dollars)
                        ->where('images', $productB->productImagesForList)
                        ->where('video', $productB->video)
                        ->where('has_variants', $productB->hasPublishedVariants())
                        ->where('variants', $productB->variants)
                        ->where('dropping_stock', false)
                        ->where('has_discounts', false)
                        ->where('discounted_price_in_dollars', '$0')
                        ->where('discounts', []);
                });
        });

});

it('does not show them, if related products are not published', function () {

    $collectionA = ProductCollection::factory()->create([
        'title' => 'Collection A',
        'slug' => 'collection-a',
    ]);
    $collectionB = ProductCollection::factory()->create([
        'title' => 'Collection B',
        'slug' => 'collection-b',
    ]);

    $collectionC = ProductCollection::factory()->create([
        'title' => 'Collection C',
        'slug' => 'collection-c',
    ]);

    $productA = Product::factory()->published()->create([
        'title' => 'Product A',
        'slug' => 'product-a',
    ]);

    $productB = Product::factory()->draft()->create([
        'title' => 'Product B',
        'slug' => 'product-b',
    ]);

    $productC = Product::factory()->published()->create([
        'title' => 'Product C',
        'slug' => 'product-c',
    ]);

    $productD = Product::factory()->published()->create([
        'title' => 'Product D',
        'slug' => 'product-d',
    ]);

    $productA->productCollections()->attach([$collectionA->id, $collectionB->id]);
    $productB->productCollections()->attach($collectionA);
    $productC->productCollections()->attach([$collectionB->id, $collectionC->id]);
    $productD->productCollections()->attach($collectionC);

    $this->get(route('storefront.product', ['product' => $productA->slug]))
        ->assertInertia(function (Assert $page) {
            return $page->has('relatedProducts', 1);
        });

});

it('shows reduced price for discounted product', function () {

    setDiscountCalculationMode();
    setStrictMode();

    $product = Product::factory()->published()->create([
        'main_image' => 'product.jpg',
        'stock_threshold_for_customers' => 10,
        'stock' => 8,
        'price' => 120,
    ]);

    $discountA = Discount::factory()->create([
        'name' => 'Discount A',
        'percentage' => 20,
        'start_date' => now()->subDay(),
        'end_date' => now()->addDays(15),
        'status' => 'active',
    ]);

    $discountB = Discount::factory()->create([
        'name' => 'Discount B',
        'percentage' => 10,
        'start_date' => now()->subDay(),
        'end_date' => now()->addDay(),
        'status' => 'active',
    ]);

    $product->discounts()->attach([$discountA->id, $discountB->id]);

    $this->get(route('storefront.product', ['product' => $product->slug]))
        ->assertInertia(function (Assert $page) use ($product, $discountA) {
            return $page->has('product', function (Assert $page) use ($product, $discountA) {
                return $page
                    ->where('id', $product->id)
                    ->where('title', $product->title)
                    ->where('description', RichContentRenderer::make($product->description)->customBlocks([
                        HeroBlock::class,
                    ])->toHtml())
                    ->where('slug', $product->slug)
                    ->where('summary', $product->summary)
                    ->where('price_in_dollars', $product->price_in_dollars)
                    ->where('has_taxes', $product->hasTaxes())
                    ->where('taxes', $product->formatted_taxes)
                    ->where('price_with_taxes_in_dollars', $product->price_with_taxes_in_dollars)
                    ->where('has_variants', $product->hasVariants())
                    ->where('variants', $product->variants)
                    ->where('main_image', Storage::url($product->main_image))
                    ->where('images', $product->productImagesForList)
                    ->where('dropping_stock', true)
                    ->where('has_discounts', true)
                    ->where('discounted_price_in_dollars', '$96')
                    ->where('discounts', [
                        [
                            'name' => $discountA->name,
                            'percentage' => $discountA->percentage,

                        ],
                    ]);
            });
        });

});
