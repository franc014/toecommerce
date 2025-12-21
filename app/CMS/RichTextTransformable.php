<?php

namespace App\CMS;

use Filament\Forms\Components\RichEditor\RichContentRenderer;

class RichTextTransformable implements ContentTransformable
{
    public function transform(array $item): array
    {
        if (isset($item['type']) && $item['type'] === 'rich-editor') {
            $item['data']['content'] = RichContentRenderer::make($item['data']['content'])->toHtml();
        }

        return $item;
    }
}
