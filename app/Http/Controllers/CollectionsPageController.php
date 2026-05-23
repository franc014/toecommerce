<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use JFA\ToecommerceCore\Models\ProductCollection;

class CollectionsPageController extends PageController
{
    public function __construct()
    {
        $collections = ProductCollection::query()->withCount('products')->get()->map(function ($collection) {
            return [
                'id' => $collection->id,
                'title' => $collection->title,
                'slug' => $collection->slug,
                'description' => $collection->description,
                'featured_image' => Storage::url($collection->featured_image),
            ];
        });

        parent::__construct(
            componentView: 'Collections',
            slug: 'collections',
            transformables: [],
            extendedData: [
                'collections' => $collections,
            ]
        );
    }
}
