<?php

namespace App\Filament\Resources\ProductCollections;

use App\Filament\Resources\ProductCollections\Pages\CreateProductCollection;
use App\Filament\Resources\ProductCollections\Pages\EditProductCollection;
use App\Filament\Resources\ProductCollections\Pages\ListProductCollections;
use App\Filament\Resources\ProductCollections\Pages\ViewProductCollection;
use App\Filament\Resources\ProductCollections\Schemas\ProductCollectionForm;
use App\Filament\Resources\ProductCollections\Schemas\ProductCollectionInfolist;
use App\Filament\Resources\ProductCollections\Tables\ProductCollectionsTable;
use App\Models\ProductCollection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProductCollectionResource extends Resource
{
    protected static ?string $model = ProductCollection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getModelLabel(): string
    {
        return __('firesources.collection');
    }

    public static function getPluralModelLabel(): string
    {
        return __('firesources.collections');
    }

    public static function getNavigationGroup(): UnitEnum|string|null
    {
        return __('firesources.taxonomies');
    }

    protected static ?string $recordTitleAttribute = 'title';

    /* public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    } */

    public static function form(Schema $schema): Schema
    {
        return ProductCollectionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductCollectionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductCollectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductCollections::route('/'),
            // 'create' => CreateProductCollection::route('/create'),
            // 'view' => ViewProductCollection::route('/{record}'),
            // 'edit' => EditProductCollection::route('/{record}/edit'),
        ];
    }
}
