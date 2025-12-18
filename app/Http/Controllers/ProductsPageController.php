<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Settings\StorefrontSettings;
use Inertia\Inertia;

class ProductsPageController extends Controller
{
    public function __invoke(StorefrontSettings $sfSettings)
    {

        $products = Product::published()->with('variants')->paginate($sfSettings->products_per_page)->through(function ($product) {
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
            ];
        });

        ray($products);

        return Inertia::render('Products', [
            'products' => $products,
        ]);
    }
}
