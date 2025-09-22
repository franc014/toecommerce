<?php

namespace App\Filament\Resources\ProductVariants\Tables;

use App\Filament\Resources\Products\Tables\PublishingActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductVariantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.title')->limit(30)->sortable()->label('Producto'),
                ColorColumn::make('color'),
                TextColumn::make('sizes')->badge(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('price')->money('USD'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                PublishingActions::getPublishingActions(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
                PublishingActions::getBulkPublishingActions(),
            ]);
    }
}
