<?php

namespace App\Http\Controllers;

use App\Models\ProductCollection;
use App\Settings\StorefrontSettings;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CollectionPageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, ProductCollection $collection, StorefrontSettings $sfSettings)
    {

        $products = $collection->products()->published()->with('variants')->paginate($sfSettings->products_per_page)->through(function ($product) {
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

        return Inertia::render('Collection', [
            'collection' => $collection,
            'products' => $products,
        ]);
    }
}
