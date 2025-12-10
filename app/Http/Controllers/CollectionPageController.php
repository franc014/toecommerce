<?php

namespace App\Http\Controllers;

use App\Models\ProductCollection;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CollectionPageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, ProductCollection $collection)
    {
        $products = $collection->products()->published()->get()->map(function ($product) {
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
            'products' => $products,
        ]);
    }
}
