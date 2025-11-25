<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\Builder;
use Filament\Support\Enums\Alignment;

class ContentBuilder
{
    public static function make(): Builder
    {
        return Builder::make('content')
            ->label(__('firesources.content'))
            ->blockNumbers(false)
            ->blockIcons()
            ->addActionLabel(__('firesources.add_block'))
            ->addActionAlignment(Alignment::Start)
            ->collapsible()
            ->collapsed()
            ->cloneable()
            ->required()
            ->blocks([
                ContentBlocks::heading(),
                ContentBlocks::paragraph(),
                ContentBlocks::richEditor(),
                ContentBlocks::cta(),
                ContentBlocks::image(),
                ContentBlocks::newProductsChoice(),
                ContentBlocks::featuredProduct(),
                ContentBlocks::collections(),
                ContentBlocks::feature(),
            ]);

    }
}
