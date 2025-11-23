<?php

namespace App\Filament\Resources\ProductCollections\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductCollectionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title')->label(__('firesources.title')),
                TextEntry::make('slug'),
                TextEntry::make('description')->label(__('firesources.description'))
                    ->columnSpanFull(),
                ImageEntry::make('featured_image')->label(__('firesources.featured_image'))

            ]);
    }
}
