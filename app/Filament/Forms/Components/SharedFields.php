<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

trait SharedFields
{
    public static function titleAndSlugFields(string $aditionalSlugable = ''): array
    {
        return [
            TextInput::make('title')
                ->label(__('firesources.title'))
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

    public static function metatagsField(): KeyValue
    {
        return KeyValue::make('metatags')
            ->keyLabel(__('firesources.meta_tag'))
            ->addActionLabel(__('firesources.add_meta_tag'))
            ->required()
            ->keyPlaceholder(__('firesources.meta_tag'))
            ->valuePlaceholder(__('firesources.meta_tag_value'))
            ->default([
                'og_title' => '',
                'og_description' => '',
                'og_image' => '',
                'twitter_card' => 'summary_large_image',
                'twitter_title' => '',
                'twitter_description' => '',
                'twitter_image' => '',
                'robots' => 'index,follow',
            ]);
    }
}
