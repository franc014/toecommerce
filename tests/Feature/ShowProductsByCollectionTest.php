<?php

use App\Models\Product;
use App\Models\ProductCollection;
use Inertia\Testing\AssertableInertia as Assert;

it('shows products by collection', function () {

    setPaginationNumber(6);

    $collection = ProductCollection::factory()->create([
        'title' => 'Collection A',
        'slug' => 'collection-a',
    ]);

    $products = Product::factory(4)->published()->create();

    foreach ($products as $product) {
        $product->productCollections()->attach($collection->id);
    }

    $this->get(route('storefront.collection', ['collection' => $collection->slug]))
        ->assertInertia(
            fn (Assert $page) => $page
            ->has('products.data', 4)
            ->has(
                'products.data.0',
                function (Assert $page) use ($products) {
                    $page->where('id', $products[0]->id)
                        ->where('title', $products[0]->title)
                        ->where('slug', $products[0]->slug)
                        ->where('price', $products[0]->price)
                        ->where('price_in_dollars', $products[0]->price_in_dollars)
                        ->where('images', $products[0]->productImagesForList)
                        ->where('video', $products[0]->video)
                        ->where('has_variants', $products[0]->hasPublishedVariants())
                        ->where('variants', $products[0]->variants)
                        ->where('dropping_stock', false);
                }
            )
        );

});
