<?php

namespace App\Http\Controllers;

use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\HeroBlock;
use App\Models\Product;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductPageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Product $product)
    {

        $data = [
            'id' => $product->id,
            'title' => $product->title,
            'slug' => $product->slug,
            'description' => RichContentRenderer::make($product->description)->customBlocks([
                HeroBlock::class
            ])->toHtml(),
            'price_in_dollars' => $product->price_in_dollars,
            'has_taxes' => $product->hasTaxes(),
            'taxes' => $product->formatted_taxes, //todo: formatted taxes
            'price_with_taxes_in_dollars' => $product->price_with_taxes_in_dollars,
            'images' => $product->productImagesForList,
            'has_variants' => $product->hasVariants(),
            'variants' => $product->variants
        ];

        return Inertia::render('Product', [
            'product' => $data
        ]);
    }
}
