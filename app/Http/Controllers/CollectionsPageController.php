<?php

namespace App\Http\Controllers;

use App\Models\ProductCollection;
use Illuminate\Support\Facades\Storage;

class CollectionsPageController extends PageController
{
    public function __construct()
    {
        $this->slug = 'collections';
        $this->view = 'Collections';

        $collections = ProductCollection::query()->get()->map(function ($collection) {
            return [
                'id' => $collection->id,
                'title' => $collection->title,
                'slug' => $collection->slug,
                'description' => $collection->description,
                'featured_image' => Storage::url($collection->featured_image),
            ];
        });

        $this->extendedData = [
            'collections' => $collections,
        ];
    }
}
