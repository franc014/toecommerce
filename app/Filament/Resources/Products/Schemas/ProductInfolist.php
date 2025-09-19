<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('description')
                    ->columnSpanFull(),
                TextEntry::make('sku')
                    ->label('SKU')
                    ->placeholder('-'),
                TextEntry::make('slug'),
                TextEntry::make('status'),
                TextEntry::make('price')
                    ->money(),
                TextEntry::make('discount')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('stock')
                    ->numeric()
                    ->placeholder('-'),
                ImageEntry::make('main_image_path'),
                TextEntry::make('published_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('archived_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('user_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
