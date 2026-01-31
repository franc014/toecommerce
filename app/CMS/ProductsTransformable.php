<?php

namespace App\CMS;

use App\Models\Product;

class ProductsTransformable implements ContentTransformable
{
    public function transform(array $item): array
    {
        if (isset($item['type']) && $item['type'] === 'new-products') {
            $productsIds = $item['data']['products'];
            $products = Product::whereIn('id', $productsIds)->get();

            foreach ($products as $key => $product) {
                $item['data']['products'][$key] = [
                    'id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug,
                    'price' => $product->price,
                    'price_in_dollars' => $product->price_in_dollars,
                    'images' => $product->productImagesForList,
                    'has_variants' => $product->hasPublishedVariants(),
                    'variants' => $product->variants,
                    'dropping_stock' => $product->isDroppingStock(),
                    'has_discounts' => $product->hasDiscounts(),
                    'discounted_price_in_dollars' => $product->discounted_price_in_dollars,
                    'discounts' => $product->discountsForList,
                ];
            }
        }

        return $item;
    }
}
