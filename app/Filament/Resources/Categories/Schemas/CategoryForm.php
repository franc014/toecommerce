<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Filament\Forms\Components\SharedFields;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CategoryForm
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
            ]);
    }
}
