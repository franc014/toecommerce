<?php

namespace App\Filament\Resources\ProductVariants\Schemas;

use App\Enums\ProductSizes;
use App\Enums\ProductStatus;
use App\Filament\Forms\Components\SharedFields;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductVariantForm
{
    use SharedFields;

    public static function configure(Schema $schema, $productId = null): Schema
    {
        return $schema
            ->components([
                ...self::titleAndSlugFields(),
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
                    ->collection('product-variant-images'),
            ]);
    }
}
