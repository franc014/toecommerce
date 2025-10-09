<?php

namespace App\Filament\Resources\ProductVariants\Schemas;

use App\Enums\ProductStatus;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductVariantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información')
                    ->columns(3)
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        Group::make()->schema([
                            TextEntry::make('product.title')->label('Producto'),
                            TextEntry::make('title')->label('Título'),
                            TextEntry::make('status')
                                ->label('Estado de la variante')
                                ->badge()
                                ->color(function ($state) {
                                    return match ($state) {
                                        ProductStatus::DRAFT => 'warning',
                                        ProductStatus::ACTIVE => 'success',
                                        ProductStatus::ARCHIVED => 'gray',
                                    };
                                }),
                            TextEntry::make('variation')->label('Variación')->badge()->color('info'),

                        ])->columnSpan(1),

                        Group::make()->schema([
                            TextEntry::make('price')->label('Precio')->money('USD'),
                            TextEntry::make('discount')->label('Descuento')->suffix('%')->placeholder('Sin descuento'),
                        ])->columnSpan(1),

                        Group::make()->schema([
                            TextEntry::make('sku'),
                            TextEntry::make('stock'),
                        ])->columnSpan(1),

                    ])->columnSpan(3),

                Section::make('Fotos')
                    ->columnSpanFull()
                    ->collapsible()->collapsed(false)->schema([
                    SpatieMediaLibraryImageEntry::make('product_images')->label('Fotos de Variantes')
                        ->placeholder('No hay fotos todavía')
                        ->conversion('thumb')
                        ->collection('product-variant-images'),
                ]),
            ]);
    }
}
