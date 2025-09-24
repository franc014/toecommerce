<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

trait SharedFields
{
    public static function titleAndSlugFields(string $aditionalSlugable = ''): array
    {
        return [
            TextInput::make('title')
                ->required()
                ->maxLength(100)
                ->live(debounce: 500)
                ->afterStateUpdated(function (Set $set, ?string $state) use ($aditionalSlugable) {
                    $set('slug', Str::slug($state));
                    $aditionalSlugable ? $set($aditionalSlugable, Str::slug($state)) : null;
                }),
            TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(100),
        ];
    }
}
