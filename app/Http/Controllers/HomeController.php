<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class HomeController extends Controller
{

    private $slug = 'home';
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $components = [];
        $page = Page::bySlug($this->slug);

        foreach ($page->sectionsForUI() as $section) {
            $component = Str::studly($section['slug']);
            if (Arr::has($section['content'], 'image')) {
                foreach ($section['content']['image'] as $key => $image) {
                    ray($image);
                    $section['content']['image'][$key] = Storage::url($image['image']);
                }
            }
            if (Arr::has($section['content'], 'new-products')) {
                $productsIds = $section['content']['new-products'];

                foreach ($productsIds[0]['products'] as $key => $productId) {
                    $product = Product::with('variants')->find($productId);
                    $section['content']['new-products'][0]['products'][$key] = [
                        'id' => $product->id,
                        'title' => $product->title,
                        'slug' => $product->slug,
                        'price' => $product->price,
                        'price_in_dollars' => $product->price_in_dollars,
                        'images' => $product->productImagesForList,
                        'has_variants' => $product->hasPublishedVariants(),
                        'variants' => $product->variants,
                        'dropping_stock' => $product->isDroppingStock(),
                    ];
                }
            }


            $components[] = [
                'class' => $component,
                'content' => $section['content'],
            ];
        }

        return Inertia::render('Home', [
            'components' => collect($components)->keyBy('class')
        ]);
    }
}
