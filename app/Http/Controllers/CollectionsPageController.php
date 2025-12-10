<?php

namespace App\Http\Controllers;

use App\Models\ProductCollection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class CollectionsPageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return Inertia::render('Collections', [
           'collections' => ProductCollection::query()->get()->map(function ($collection) {
               return [
                   'id' => $collection->id,
                   'title' => $collection->title,
                   'slug' => $collection->slug,
                   'description' => $collection->description,
                   'featured_image' => Storage::url($collection->featured_image),
               ];
           }),
        ]);
    }
}
