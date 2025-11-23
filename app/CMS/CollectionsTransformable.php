<?php

namespace App\CMS;

use App\Models\Product;
use App\Models\ProductCollection;
use Illuminate\Support\Facades\Storage;

class CollectionsTransformable implements ContentTransformable
{
    public function transform(array $item): array
    {
        if (isset($item['type']) && $item['type'] === 'collections') {
            $collectionsIds = $item['data']['collections'];
            $collections = ProductCollection::whereIn('id', $collectionsIds)->get();

            foreach ($collections as $key => $collection) {
                $item['data']['collections'][$key] = [
                    'id' => $collection->id,
                    'title' => $collection->title,
                    'slug' => $collection->slug,
                    'featured_image' => $collection->featured_image ? Storage::url($collection->featured_image) : '',
                    'products_count' => $collection->products()->count(),
                ];
            }
        }
        return $item;
    }
}
