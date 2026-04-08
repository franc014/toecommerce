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

    public function __construct(protected readonly string $componentView, protected readonly string $slug, protected readonly array $transformables, protected readonly array $extendedData = []) {}

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

            return Inertia::render($this->componentView, [
                'components' => fn () => collect($components)->keyBy('class'),
                'metatags' => fn () => $this->metatags(),
                ...$this->extendedData,
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
