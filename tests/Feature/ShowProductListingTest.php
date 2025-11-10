<?php

use App\Enums\StockControlModes;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\HeroBlock;
use App\Models\Product;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

it('shows a listing of a published product', function () {
    $this->withoutExceptionHandling();

    $product = Product::factory()->published()->create([
        'main_image' => 'product.jpg',
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
                    ->where('price_in_dollars', $product->price_in_dollars)
                    ->where('has_taxes', $product->hasTaxes())
                    ->where('taxes', $product->formatted_taxes)
                    ->where('price_with_taxes_in_dollars', $product->price_with_taxes_in_dollars)
                    ->where('has_variants', $product->hasVariants())
                    ->where('variants', $product->variants)
                    ->where('main_image', Storage::url($product->main_image))
                    ->where('images', $product->productImagesForList)
                    ->where('dropping_stock', false);
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
                    ->where('price_in_dollars', $product->price_in_dollars)
                    ->where('has_taxes', $product->hasTaxes())
                    ->where('taxes', $product->formatted_taxes)
                    ->where('price_with_taxes_in_dollars', $product->price_with_taxes_in_dollars)
                    ->where('has_variants', $product->hasVariants())
                    ->where('variants', $product->variants)
                    ->where('main_image', Storage::url($product->main_image))
                    ->where('images', $product->productImagesForList)
                    ->where('dropping_stock', true);
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
                    ->where('price_in_dollars', $product->price_in_dollars)
                    ->where('has_taxes', $product->hasTaxes())
                    ->where('taxes', $product->formatted_taxes)
                    ->where('price_with_taxes_in_dollars', $product->price_with_taxes_in_dollars)
                    ->where('has_variants', $product->hasVariants())
                    ->where('variants', $product->variants)
                    ->where('main_image', Storage::url($product->main_image))
                    ->where('images', $product->productImagesForList)
                    ->where('dropping_stock', false);
            });
        });

});

it('does not show warning text if product stock is dropping below threshold, in nonstrict mode', function () {

    $this->withoutExceptionHandling();

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
                    ->where('price_in_dollars', $product->price_in_dollars)
                    ->where('has_taxes', $product->hasTaxes())
                    ->where('taxes', $product->formatted_taxes)
                    ->where('price_with_taxes_in_dollars', $product->price_with_taxes_in_dollars)
                    ->where('has_variants', $product->hasVariants())
                    ->where('variants', $product->variants)
                    ->where('main_image', Storage::url($product->main_image))
                    ->where('images', $product->productImagesForList)
                    ->where('dropping_stock', false);
            });
        });

});
