<?php

namespace App\CMS;

use App\Models\Section;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

class ContentResolver
{
    private ?MediaCollection $images;

    public function __construct(private Section $section)
    {
        $this->images = $section->hasMedia('*') ? $section->getMedia('*') : null;
    }

    public function resolve()
    {

        $contentByType = collect($this->section->content)->groupBy('type');

        $content = $contentByType->map(function ($content) {
            return $content->pluck('data')->all();
        });

        return array_merge($content->toArray(), ['images' => $this->images]);
    }
}
