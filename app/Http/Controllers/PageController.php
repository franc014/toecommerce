<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Traits\Metatags;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\Str;
use Inertia\Inertia;

abstract class PageController extends Controller
{
    use Metatags;

    private ?Page $page;
    protected array $extendedData = [];
    protected array $transformables = [];
    protected string $view;
    protected string $slug;


    public function __invoke()
    {
        try {

            $components = [];
            $page = Page::bySlug($this->slug);

            $this->page = $page;

            foreach ($page->sectionsForUI($this->transformables) as $section) {
                $component = Str::studly($section['slug']);
                $components[] = [
                    'class' => $component,
                    'content' => $section['content'],
                ];
            }

            return Inertia::render($this->view, [
                'components' => collect($components)->keyBy('class'),
                'metatags' => $this->metatags(),
                ...$this->extendedData
            ]);

        } catch (ModelNotFoundException $e) {
            Log::error('Model not found');
            abort(404);
        } catch (ItemNotFoundException $e) {
            Log::error('the error here: item not found');
            abort(404);
        }
    }
}
