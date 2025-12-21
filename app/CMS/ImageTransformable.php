<?php

namespace App\CMS;

use Illuminate\Support\Facades\Storage;

class ImageTransformable implements ContentTransformable
{
    public function transform(array $item): array
    {
        if (isset($item['type']) && $item['type'] === 'image') {
            $item['data']['image'] = Storage::url($item['data']['image']);
        }

        return $item;
    }
}
