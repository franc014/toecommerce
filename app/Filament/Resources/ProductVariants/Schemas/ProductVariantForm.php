<?php

namespace App\Filament\Resources\ProductVariants\Schemas;

use App\Enums\ProductSizes;
use App\Enums\ProductStatus;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ProductVariantForm
{
    public static function configure(Schema $schema, $productId = null): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                ->label('Título')
                ->unique(ignoreRecord: true)
                ->live(onBlur: true)
                ->debounce(200)
                ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                    //self::generateSlug($get, $set, $old, $state);
                })
                ->required()
                ->maxLength(255),
                    TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            ColorPicker::make('color')
                ->regex('/^#([a-f0-9]{6}|[a-f0-9]{3})\b$/')
                ->default('#000000'),

            CheckboxList::make('sizes')
                ->options(ProductSizes::class)
                ->columns(3)
                ->gridDirection('row'),

            TextInput::make('price')
                ->required()
                ->numeric()
                ->inputMode('decimal')
                ->prefix('$'),
            TextInput::make('discount')
                ->numeric()
                ->minValue(0.01)
                ->maxValue(100)
                ->inputMode('decimal')
                ->suffix('%'),
            Select::make('status')->options(ProductStatus::class)
                ->default(ProductStatus::DRAFT),
            TextInput::make('sku'),
            TextInput::make('stock')->numeric()->minValue(0),
            Select::make('product_id')->hidden(function () use ($productId) {
                return $productId !== null;
            })->relationship('product', 'title')->required(),
            SpatieMediaLibraryFileUpload::make('product_variant_images')
                ->multiple()
                ->reorderable()
                ->collection('product-variant-images')
            ]);
    }
}
