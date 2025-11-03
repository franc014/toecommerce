<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Información general')
                            ->columns(2)
                            ->icon(Heroicon::OutlinedCube)
                            ->schema([
                                TextEntry::make('title'),
                                TextEntry::make('slug'),
                                TextEntry::make('description'),
                                TextEntry::make('status')
                                    ->label('')
                                    ->badge(),
                            ]),
                        Tab::make('Colecciones, categorías y etiquetas')
                            ->icon(Heroicon::OutlinedTag)
                            ->schema([
                                    TextEntry::make('tags.name')
                                        ->label('Etiquetas')
                                        ->placeholder('No hay etiquetas todavía')->badge()->color('primary'),
                                    TextEntry::make('categories.title')->label('Categorías')->badge(),
                                    TextEntry::make('productCollections.title')->label('Colecciones')->badge(),
                            ]),
                        Tab::make('Precios, stock e impuestos')
                            ->icon(Heroicon::OutlinedCurrencyDollar)
                            ->columns(2)
                            ->schema([
                                    TextEntry::make('price')->money('USD'),
                                    TextEntry::make('taxes.name')
                                        ->placeholder('No hay impuestos asignados todavía')
                                        ->label('Impuestos asignados')->badge()->color('primary'),
                                    TextEntry::make('stock'),
                                    TextEntry::make('stock_threshold_for_customers')
                                    ->label('Umbral de stock en descenso para clientes'),
                                    TextEntry::make('sku'),
                            ]),
                        Tab::make('Imágenes')
                            ->icon(Heroicon::OutlinedPhoto)
                            ->schema([
                                    SpatieMediaLibraryImageEntry::make('product_images')->label('')
                                        ->label('Fotos')
                                        ->placeholder('No hay fotos todavía')
                                        ->conversion('thumb')
                                        ->collection('product-images'),
                            ]),
                    ]),

            ]);
    }
}
