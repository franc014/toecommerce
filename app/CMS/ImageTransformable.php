<?php

namespace App\CMS;

use App\CMS\ContentTransformable;
use Illuminate\Support\Facades\Storage;

class ImageTransformable implements ContentTransformable
{
    public function transform(array $item): array
    {
        if (isset($item['data']['image'])) {
            $item['data']['image'] = Storage::url($item['data']['image']);
        }

        return $item;
    }
}
