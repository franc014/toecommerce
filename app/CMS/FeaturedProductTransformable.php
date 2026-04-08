<?php

namespace App\CMS;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class FeaturedProductTransformable implements ContentTransformable
{
    public function transform(array $item): array
    {
        if (isset($item['type']) && $item['type'] === 'featured-product') {
            $productsId = $item['data']['product'];
            $product = Product::find($productsId);

            $item['data']['product'] = [
                'id' => $product->id,
                'title' => $product->title,
                'slug' => $product->slug,
                'price' => $product->price,
                'price_in_dollars' => $product->price_in_dollars,
                'images' => $product->productImagesForList,
                'main_image' => $product->main_image ? Storage::url($product->main_image) : '',
                'video' => $product->video,
                'has_variants' => $product->hasPublishedVariants(),
                'variants' => $product->variants,
                'dropping_stock' => $product->isDroppingStock(),
                'has_discounts' => $product->has_discounts,
                'discounted_price_in_dollars' => $product->discounted_price_in_dollars,
                'discounts' => $product->discountsForList,
            ];

        }

        return $item;
    }
}
