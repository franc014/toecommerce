<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            $components[] = [
                'class' => $component,
                'content' => $section['content'],
            ];
        }



        $heroImg = Storage::url('demos/dog-model-2.png');
        $latestProducts = Product::published()->with('variants')->take(4)->get()->map(function ($product) {
            return [
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
        });

        ray($components);

        return Inertia::render('Home', [
            'heroImage' => $heroImg,
            'latestProducts' => $latestProducts,
            'components' => $components
        ]);
    }
}
