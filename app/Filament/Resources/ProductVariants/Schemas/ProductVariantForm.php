<?php

namespace App\Filament\Resources\ProductVariants\Schemas;

use App\Enums\ProductStatus;
use App\Filament\Forms\Components\SharedFields;
use Filament\Forms\Components\RichEditor;
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
                RichEditor::make('description')
                    ->label(__('firesources.description'))
                    ->maxLength(2048)
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->label(__('firesources.price'))
                    ->required()
                    ->numeric()
                    ->inputMode('decimal')
                    ->prefix('$'),
                TextInput::make('discount')
                    ->label(__('firesources.discount'))
                    ->numeric()
                    ->minValue(0.01)
                    ->maxValue(100)
                    ->inputMode('decimal')
                    ->suffix('%'),
                Select::make('status')
                    ->label(__('firesources.status'))
                    ->options(ProductStatus::class)
                    ->default(ProductStatus::DRAFT),
                TextInput::make('sku')
                    ->required(),
                TextInput::make('stock')
                    ->label(__('firesources.stock'))
                    ->numeric()
                    ->minValue(0),
                Select::make('product_id')
                    ->label(__('firesources.product'))
                    ->hidden(function () use ($productId) {
                        return $productId !== null;
                    })->relationship('product', 'title')->required(),
                SpatieMediaLibraryFileUpload::make('product_variant_images')
                    ->label(__('firesources.images'))
                    ->multiple()
                    ->reorderable()
                    ->collection('product-variant-images'),
            ]);
    }
}
