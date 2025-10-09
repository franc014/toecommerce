<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Filament\Resources\ProductVariants\Schemas\ProductVariantForm;
use App\Filament\Resources\ProductVariants\Schemas\ProductVariantInfolist;
use App\Filament\Resources\ProductVariants\Tables\ProductVariantsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    public function form(Schema $schema): Schema
    {
        return ProductVariantForm::configure($schema, $this->getOwnerRecord()->id);
    }

    public function table(Table $table): Table
    {
        return ProductVariantsTable::configure($table);
    }

    public function infolist(Schema $schema): Schema
    {
        return ProductVariantInfolist::configure($schema);
    }

    public static function getTabComponent(Model $ownerRecord, string $pageClass): Tab
    {
        return Tab::make('Variantes')->icon(Heroicon::OutlinedSwatch);
    }
}
