<?php

namespace App\CMS;

use App\Models\Section;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Illuminate\Support\Facades\Storage;

class ContentResolver
{
    private ?MediaCollection $images;

    public function __construct(private Section $section)
    {
        $this->images = $section->hasMedia('*') ? $section->getMedia('*') : null;
    }

    //Array of interface ContentTransformable instances
    public function resolve(array $transformables = []): array
    {

        $data = $this->section->content;


        $data = collect($data)->map(function ($item) use ($transformables) {

            foreach ($transformables as $transformable) {
                $transormedItem = $transformable->transform($item);
                $item = $transormedItem;
            }

            return $item;
        });


        $contentByType = $data->groupBy('type');

        $content = $contentByType->map(function ($content) {
            return $content->pluck('data')->all();
        });

        return array_merge($content->toArray(), ['images' => $this->images]);
    }
}
