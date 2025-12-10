<?php

namespace App\Http\Controllers;

use App\CMS\CollectionsTransformable;
use App\CMS\FeaturedProductTransformable;
use App\CMS\FeatureTransformable;
use App\CMS\ImageTransformable;
use App\CMS\ProductsTransformable;
use App\Models\Page;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\Str;
use Inertia\Inertia;

class HomeController extends Controller
{
    private $slug = 'home';

    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {


        try {

            $components = [];
            $page = Page::bySlug($this->slug);

            foreach ($page->sectionsForUI([new ImageTransformable, new ProductsTransformable, new FeaturedProductTransformable, new CollectionsTransformable, new FeatureTransformable]) as $section) {
                $component = Str::studly($section['slug']);
                $components[] = [
                    'class' => $component,
                    'content' => $section['content'],
                ];
            }

            return Inertia::render('Home', [
                'components' => collect($components)->keyBy('class'),
            ]);

        } catch (ModelNotFoundException $e) {
            ray('the error here: model not found');
            abort(404);
        } catch (ItemNotFoundException $e) {
            ray('the error here: item not found');
            abort(404);
        }
    }
}
