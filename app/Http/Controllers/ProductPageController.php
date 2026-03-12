<?php

namespace App\Http\Controllers;

use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\HeroBlock;
use App\Models\Product;
use App\Traits\Metatags;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Spatie\SchemaOrg\Schema;

class ProductPageController extends Controller
{
    use Metatags;

    private Product $product;

    /**
     * Handle the incoming request.
     */
    public function __invoke(Product $product)
    {
        $this->product = $product;

        $data = [
            'id' => $product->id,
            'title' => $product->title,
            'slug' => $product->slug,
            'summary' => $product->summary,
            'description' => RichContentRenderer::make($product->description)->customBlocks([
                HeroBlock::class,
            ])->toHtml(),
            'price_in_dollars' => $product->price_in_dollars,
            'has_taxes' => $product->hasTaxes(),
            'taxes' => $product->formatted_taxes, // todo: formatted taxes
            'price_with_taxes_in_dollars' => $product->price_with_taxes_in_dollars,
            'images' => $product->productImagesForList,
            'has_variants' => $product->hasVariants(),
            'variants' => $product->variants,
            'main_image' => Storage::url($product->main_image),
            'dropping_stock' => $product->isDroppingStock(),
            'has_discounts' => $product->has_discounts,
            'discounted_price_in_dollars' => $product->discounted_price_in_dollars,
            'discounts' => $product->discountsForList,
        ];

        $relatedProducts = $product->relatedProducts()?->map(function ($product) {
            return [
                'id' => $product->id,
                'title' => $product->title,
                'slug' => $product->slug,
                'price' => $product->price,
                'price_in_dollars' => $product->price_in_dollars,
                'images' => $product->productImagesForList,
                'video' => $product->video,
                'has_variants' => $product->hasPublishedVariants(),
                'variants' => $product->variants,
                'dropping_stock' => $product->isDroppingStock(),
                'has_discounts' => $product->has_discounts,
                'discounted_price_in_dollars' => $product->discounted_price_in_dollars,
                'discounts' => $product->discountsForList,
            ];
        });

        return Inertia::render('Product', [
            'product' => $data,
            'relatedProducts' => $relatedProducts,
            'metatags' => $this->metatags(),
        ]);
    }

    private function title()
    {
        return $this->product->title;
    }

    private function description()
    {
        return $this->product->excerpt ?? '';
    }

    private function og_title()
    {
        return $this->product->title;
    }

    private function og_description()
    {
        return $this->product->excerpt ?? '';
    }

    private function og_image()
    {
        return Storage::url($this->product->main_image);
    }

    private function twitter_card()
    {
        return 'summary_large_image';
    }

    private function twitter_title()
    {
        return $this->product->title;
    }

    private function twitter_description()
    {
        return $this->product->excerpt ?? '';
    }

    private function twitter_image()
    {
        return Storage::url($this->product->main_image);
    }

    private function robots()
    {
        return 'index,follow';
    }

    private function schema_org()
    {
        $sp = Schema::Product()->name($this->product->title)
            ->sameAs(route('storefront.product', ['product' => $this->product->slug]))
            ->image(Storage::url($this->product->main_image))
            ->description($this->product->excerpt ?? '')
            ->keywords($this->product->tags()->pluck('name')->implode(', '));

        return json_encode($sp);
    }
}
