<?php

namespace App\Filament\Resources\ProductVariants\Tables;

use App\Filament\Actions\BulkDiscountsAction;
use App\Filament\Actions\DiscountsAction;
use App\Filament\Resources\Products\Tables\PublishingActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductVariantsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->columns([
                TextColumn::make('product.title')->label(__('firesources.product'))->limit(30)->sortable()->searchable(),
                TextColumn::make('title')->label(__('firesources.variant'))->limit(30)->sortable()->searchable(),
                TextColumn::make('status')->label(__('firesources.status'))->badge()->sortable(),
                TextColumn::make('price')->label(__('firesources.price'))->sortable()->searchable()->money('USD'),
            ])
            ->filters([
                //
            ])

            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DiscountsAction::make(),
                PublishingActions::getPublishingActions(),
            ])
            ->toolbarActions([
                BulkDiscountsAction::make(),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
                PublishingActions::getBulkPublishingActions(),
            ]);
    }
}
