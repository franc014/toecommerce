<?php

namespace App\Filament\Resources\ProductCollections\Schemas;

use App\Filament\Forms\Components\SharedFields;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProductCollectionForm
{
    use SharedFields;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...self::titleAndSlugFields(),
                Textarea::make('description')
                    ->label(__('firesources.description'))
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('featured_image')
                    ->label(__('firesources.featured_image'))
                    ->directory('images')
                    ->maxSize(1024 * 3)
                    ->image()
                    ->visibility('public')
                    ->columnSpanFull(),

            ]);
    }
}
