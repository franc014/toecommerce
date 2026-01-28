<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Discount;
use App\Models\Product;
use App\Models\Tax;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
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
use Illuminate\Database\Eloquent\Collection;

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
                Action::make('discounts')
                    ->label(__('firesources.discounts'))
                    ->icon(Heroicon::OutlinedTag)
                    ->color('success')
                    ->schema([
                        CheckboxList::make('discounts')
                            ->label(__('firesources.discounts'))
                            ->options(function () {
                                return Discount::valid()->pluck('name', 'id');
                            }),
                    ])
                    ->fillForm(function (Product $record) {
                        return [
                            'discounts' => $record->discounts()->get()->pluck('id'),
                        ];
                    })
                    ->action(function (Product $record, array $data) {
                        $record->discounts()->sync($data['discounts']);
                    })
                    ->after(function () {
                        return Notification::make()
                            ->success()
                            ->title(__('firesources.discounts_updated'))->send();
                    }),

                EditAction::make(),

                PublishingActions::getPublishingActions(),

            ])
            ->toolbarActions([

                BulkAction::make('assignTaxes')
                    ->label(__('firesources.assign_taxes'))
                    ->schema([
                        CheckboxList::make('taxes')->options(Tax::query()->pluck('name', 'id'))
                            ->label(__('firesources.taxes'))
                            ->default([]),
                    ])
                    ->action(function (array $data, Collection $records) {
                        foreach ($records as $record) {
                            $record->taxes()->sync($data['taxes']);
                        }
                    })->after(function () {
                        return Notification::make()
                            ->success()
                            ->title('Impuestos asignados')->send();
                    })
                    ->color('info')
                    ->icon(Heroicon::OutlinedPercentBadge),

                BulkAction::make('assignDiscounts')
                    ->label(__('firesources.assign_discounts'))
                    ->schema([
                        CheckboxList::make('discounts')->options(Discount::valid()->pluck('name', 'id'))
                            ->label(__('firesources.discounts'))
                            ->default([]),
                    ])
                    ->action(function (array $data, Collection $records) {
                        foreach ($records as $record) {
                            $record->discounts()->sync($data['discounts']);
                        }
                    })->after(function () {
                        return Notification::make()
                            ->success()
                            ->title(__('firesources.discounts_updated'))->send();
                    })
                    ->color('success')
                    ->icon(Heroicon::OutlinedTag),
                DeleteBulkAction::make(),

                PublishingActions::getBulkPublishingActions(),

            ]);
    }
}
