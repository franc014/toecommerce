<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\Str;
use Inertia\Inertia;

abstract class PageController extends Controller
{
    public function __invoke()
    {
        try {

            $components = [];
            $page = Page::bySlug($this->slug);

            foreach ($page->sectionsForUI($this->transformables) as $section) {
                $component = Str::studly($section['slug']);
                $components[] = [
                    'class' => $component,
                    'content' => $section['content'],
                ];
            }

            return Inertia::render($this->view, [
                'components' => collect($components)->keyBy('class'),
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
