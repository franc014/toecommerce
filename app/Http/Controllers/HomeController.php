<?php

namespace App\Http\Controllers;

use App\CMS\FeaturedProductTransformable;
use App\CMS\ImageTransformable;
use App\CMS\ProductsTransformable;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

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

        foreach ($page->sectionsForUI([new ImageTransformable, new ProductsTransformable, new FeaturedProductTransformable]) as $section) {
            $component = Str::studly($section['slug']);
            $components[] = [
                'class' => $component,
                'content' => $section['content'],
            ];
        }

        return Inertia::render('Home', [
            'components' => collect($components)->keyBy('class'),
        ]);
    }
}
