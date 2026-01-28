<?php

namespace App\Filament\Resources\Discounts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DiscountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('firesources.name'))
                    ->required(),
                Textarea::make('description')
                    ->label(__('firesources.description'))
                    ->rows(3),
                TextInput::make('percentage')
                    ->label(__('firesources.discount').' (%)')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('start_date')
                    ->label(__('firesources.start_date'))
                    ->required(),
                DateTimePicker::make('end_date')
                    ->label(__('firesources.end_date'))
                    ->required(),
            ]);
    }
}
