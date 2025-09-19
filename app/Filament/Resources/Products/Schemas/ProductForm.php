<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('sku')
                    ->label('SKU'),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('status')
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('discount')
                    ->numeric(),
                TextInput::make('stock')
                    ->numeric(),
                TextInput::make('tags'),
                FileUpload::make('main_image_path')
                    ->image()
                    ->required(),
                DateTimePicker::make('published_at'),
                DateTimePicker::make('archived_at'),
                TextInput::make('user_id')
                    ->numeric(),
            ]);
    }
}
