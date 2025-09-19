<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->persistFiltersInSession()
            ->filtersTriggerAction(function ($action) {
                return $action->button()->label('Filtros');
            })
            ->emptyStateHeading('No hay productos en este listado')
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->limit(20)
                    ->sortable()
                    ->searchable()
                    ->description(function (Product $record) {
                        return str()->limit($record->description, 40);
                    })->wrap(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Precio')
                    ->money()
                    ->sortable(),

                ImageColumn::make('main_image_path')
                    ->label('Imagen principal'),
                TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Fecha de actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('archived_at')
                    ->label('Fecha de archivo')
                    ->dateTime()
                    ->sortable()
                     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('has_variants')->query(function ($query) {
                    return $query->has('productVariants');
                })->label('Con variantes')->toggle(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
