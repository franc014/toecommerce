<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Settings\StorefrontSettings;

class ProductsPageController extends PageController
{
    public function __construct(StorefrontSettings $sfSettings)
    {
        $this->view = 'Products';
        $this->slug = 'products';

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
                'has_discounts' => $product->hasDiscounts(),
                'discounted_price_in_dollars' => $product->discounted_price_in_dollars,
                'discounts' => $product->discountsForList,
            ];
        });

        $this->extendedData = [
            'products' => fn () => $products,
        ];

    }
}
