<?php

use App\Models\ProductCollection;
use Illuminate\Support\Facades\Storage;

use Inertia\Testing\AssertableInertia as Assert;

test('can show a list of collections', function () {

    $this->withoutExceptionHandling();

    $totalCollections = 6;
    $collections = ProductCollection::factory($totalCollections)->create([
        'featured_image' => 'test.jpg',
    ]);

    $this->get(route('storefront.collections'))->assertInertia(
        fn (Assert $page) => $page
            ->has('collections', $totalCollections)
            ->has(
                'collections.0',
                function (Assert $page) use ($collections) {
                    $page->where('id', $collections[0]->id)
                        ->where('title', $collections[0]->title)
                        ->where('slug', $collections[0]->slug)
                        ->where('description', $collections[0]->description)
                        ->where('featured_image', Storage::url($collections[0]->featured_image));
                }
            )
    );
});
