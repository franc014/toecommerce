<?php

namespace App\Filament\Resources\Products\Tables;

use App\Filament\Actions\BulkDiscountsAction;
use App\Filament\Actions\DiscountsAction;
use App\Models\Product;
use App\Models\Tax;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
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
                return $action->button()->label(__('firesources.filters'));
            })
            ->emptyStateHeading('No hay productos en este listado')
            ->columns([
                TextColumn::make('title')
                    ->label(__('firesources.title'))
                    ->limit(20)
                    ->sortable()
                    ->searchable()
                /*  ->description(function (Product $record) {
                        return str()->limit($record->description, 40);
                    })->wrap() */,
                TextColumn::make('status')
                    ->label(__('firesources.status'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->label(__('firesources.price'))
                    ->money()
                    ->sortable(),

                /*  ImageColumn::make('main_image')
                    ->label(__('firesources.image'))
                    ->circular()
                    ->imageSize(60), */

                TextColumn::make('productCollections.title')
                    ->label(__('firesources.collections'))
                    ->badge()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label(__('firesources.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('firesources.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('archived_at')
                    ->label(__('firesources.archived_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('has_variants')->query(function ($query) {
                    return $query->has('productVariants');
                })->label(__('firesources.with_variants'))->toggle(),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('variants')
                    ->label(__('firesources.variants'))
                    ->visible(function (Product $product) {
                        return $product->hasPublishedVariants();
                    })
                    ->url(function (Product $product) {
                        // return "#";
                        return route('filament.admin.resources.products.variants', ['record' => $product]);
                    })->icon(Heroicon::OutlinedSwatch),
                Action::make('taxes')
                    ->label(__('firesources.taxes'))
                    ->icon(Heroicon::OutlinedReceiptPercent)
                    ->color('info')
                    ->schema([
                        CheckboxList::make('taxes')
                            ->label(__('firesources.taxes'))
                            ->options(function () {
                                return Tax::all()->pluck('name', 'id');
                            }),
                    ])
                    ->fillForm(function (Product $record) {
                        return [
                            'taxes' => $record->taxes()->get()->pluck('id'),
                        ];
                    })
                    ->action(function (Product $record, array $data) {
                        $record->taxes()->sync($data['taxes']);
                    })
                    ->after(function () {
                        return Notification::make()
                            ->success()
                            ->title(__('firesources.taxes_updated'))->send();
                    }),
                DiscountsAction::make(),

                EditAction::make(),

                PublishingActions::getPublishingActions(),

            ])
            ->toolbarActions([

                BulkDiscountsAction::make(),

                DeleteBulkAction::make(),

                PublishingActions::getBulkPublishingActions(),

            ]);
    }
}
