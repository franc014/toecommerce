<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\ProductStatus;
use App\Models\Tax;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use App\Filament\Forms\Components\SharedFields;

class ProductForm
{
    use SharedFields;
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Tabs::make('Producto')
                ->columnSpanFull()
                ->persistTab()
                ->id('product-tabs')
                ->columns(2)
                ->tabs([
                    Tab::make('Información general')
                    ->icon(Heroicon::OutlinedCube)
                    ->schema(
                        [
                            ...self::titleAndSlugFields(),
                             Select::make('status')
                                    ->required()
                                    ->default(ProductStatus::DRAFT)
                                    ->enum(ProductStatus::class)
                                    ->options(ProductStatus::class),
                            Textarea::make('description')
                                ->required()
                                ->columnSpanFull(),
                            TextInput::make('sku')->label('SKU'),
                        ]
                    ),
                    Tab::make('Colecciones, Categorias y Etiquetas')
                        ->icon(Heroicon::OutlinedTag)->schema([
                            SpatieTagsInput::make('tags')->label('Etiquetas'),
                            Select::make('product_collections')
                                ->label('Colecciones')
                                ->multiple()
                                ->relationship('productCollections', 'title'),

                            Select::make('category_id')
                                ->label('Categorias')
                                ->multiple()
                                ->relationship('categories', 'title'),
                    ]),
                    Tab::make('Precio, stock e impuestos')
                        ->icon(Heroicon::OutlinedCurrencyDollar)->schema([
                            TextInput::make('price')
                            ->label('Precio')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10000)
                            ->inputMode('decimal')
                            ->prefix('$'),


                        TextInput::make('discount')
                            ->label('Descuento')
                            ->numeric()
                            ->minValue(0.01)
                            ->maxValue(100)
                            ->inputMode('decimal')
                            ->prefix('%'),

                        CheckboxList::make('taxes')
                            ->label('Impuestos')
                            ->getOptionLabelFromRecordUsing(fn (Tax $record) => "{$record->name} [{$record->percentage} %]")
                            ->relationship('taxes', 'name'),

                        TextInput::make('stock')
                            ->label('Stock')
                            ->required()
                            ->numeric()
                            ->step(1),
                    ]),
                    Tab::make('Imagenes')

                        ->icon(Heroicon::OutlinedPhoto)->schema([
                        SpatieMediaLibraryFileUpload::make('product_images')
                            ->label('Imágenes')
                            ->image()
                            ->required()
                            ->maxSize(1024 * 3)
                            ->minFiles(1)
                            ->maxFiles(5)
                            ->multiple()
                            ->imageEditor()
                            ->panelLayout('grid')
                            ->uploadingMessage('Cargando imagenes...')
                            ->reorderable()
                            ->manipulations([
                                'thumb' => ['orientation' => '90', 'width' => 200, 'height' => 200],
                            ])
                            ->responsiveImages()
                            ->columnSpanFull()
                            ->visibility('public')
                            ->conversion('thumb')
                            ->collection('product-images')

                    ]),
                ]),

            ]);
    }
}
