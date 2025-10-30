<?php

use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\HeroBlock;
use App\Models\Product;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
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
                    ->where('description', RichContentRenderer::make($product->description)->customBlocks([
                        HeroBlock::class
                    ])->toHtml())
                    ->where('slug', $product->slug)
                    ->where('price_in_dollars', $product->price_in_dollars)
                    ->where('has_taxes', $product->hasTaxes())
                    ->where('taxes', $product->formatted_taxes)
                    ->where('price_with_taxes_in_dollars', $product->price_with_taxes_in_dollars)
                    ->where('has_variants', $product->hasVariants())
                    ->where('variants', $product->variants)
                    ->where('images', $product->productImagesForList);
            });
        });

});
