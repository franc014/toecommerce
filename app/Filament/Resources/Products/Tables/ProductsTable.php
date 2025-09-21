<?php

namespace App\Filament\Resources\Products\Tables;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\Tax;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
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
                Action::make('variants')
                    ->label('Variantes')
                    ->visible(function (Product $product) {
                        return $product->hasVariants();
                    })
                    ->url(function (Product $product) {
                        return "#";
                        ///return route('filament.admin.resources.products.productVariants', ['record' => $product]);
                    })->icon(Heroicon::OutlinedSwatch),
                Action::make('taxes')->label('Impuestos')
                    ->icon(Heroicon::OutlinedReceiptPercent)
                    ->color('info')
                    ->schema([
                        CheckboxList::make('taxes')
                            ->label('Impuestos')
                            ->options(function () {
                                return Tax::all()->pluck('name', 'id');
                            })
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
                            ->title('Impuestos actualizados')->send();
                    }),
                EditAction::make(),

                ActionGroup::make([
                    ReplicateAction::make()
                        ->label('Duplicar')
                        ->successNotificationTitle('Producto duplicado')->size('xs'),

                    Action::make('publish')->action(function (Product $product) {
                        $product->publish();
                    })->icon(Heroicon::OutlinedArrowUpOnSquare)
                        ->visible(function (Product $product) {
                            return $product->status === ProductStatus::DRAFT || $product->status === ProductStatus::ARCHIVED;
                        })
                        ->label('Publicar')
                        ->color('success')
                        ->after(function () {
                            return Notification::make()
                                ->success()
                                ->title('Producto publicado')->send();
                        }),

                    Action::make('unpublish')->action(function (Product $product) {
                        $product->unpublish();
                    })->icon(Heroicon::OutlinedArrowDownOnSquare)
                        ->visible(function (Product $product) {
                            return $product->status === ProductStatus::ACTIVE;
                        })
                        ->label('Borrador')
                        ->color('warning')
                        ->after(function () {
                            return Notification::make()
                                ->success()
                                ->title('Producto despublicado')->send();
                        })->requiresConfirmation(),

                    Action::make('archive')->action(function (Product $product) {
                        $product->archive();
                    })->icon(Heroicon::OutlinedArchiveBoxArrowDown)
                        ->label('Archivar')
                        ->color('gray')
                        ->visible(function (Product $product) {
                            return $product->status === ProductStatus::ACTIVE || $product->status === ProductStatus::DRAFT;
                        })
                        ->after(function () {
                            return Notification::make()
                                ->success()
                                ->title('Producto archivado')->send();
                        })->requiresConfirmation(),
                   DeleteAction::make()->label('Eliminar')->color('danger')->size('xs'),
                ])

            ])
            ->toolbarActions([

                    BulkAction::make('assignTaxes')
                    ->schema([
                        CheckboxList::make('taxes')->options(Tax::query()->pluck('name', 'id'))
                            ->label('Impuestos')
                            ->default([])
                    ])
                    ->action(function (array $data, Collection $records) {
                        foreach ($records as $record) {
                            $record->taxes()->sync($data['taxes']);
                        }
                    })->label('Asignar impuestos')->after(function () {
                        return Notification::make()
                            ->success()
                            ->title('Impuestos asignados')->send();
                    })
                    ->color('info')
                    ->icon(Heroicon::OutlinedPercentBadge),
                    DeleteBulkAction::make(),


                BulkActionGroup::make([

                    BulkAction::make('publish')->action(function (Collection $records) {
                        foreach ($records as $record) {
                            $record->publish();
                        }
                    })->icon(Heroicon::OutlinedArrowUpOnSquare)
                      ->color('success')
                      ->label('Publicar')
                      ->after(function () {
                          return Notification::make()
                              ->success()
                              ->title('Productos publicados')->send();
                      }),

                    BulkAction::make('unpublish')->action(function (Collection $records) {
                        foreach ($records as $record) {
                            $record->unpublish();
                        }
                    })->icon(Heroicon::OutlinedArrowDownOnSquare)->color('warning')->label('Borrador')->after(function () {
                        return Notification::make()
                            ->success()
                            ->title('Productos despublicados')->send();
                    }),
                    BulkAction::make('archive')->action(function (Collection $records) {
                        foreach ($records as $record) {
                            $record->archive();
                        }
                    })->icon(Heroicon::OutlinedArchiveBoxArrowDown)->color('gray')->label('Archivar')->after(function () {
                        return Notification::make()
                            ->success()
                            ->title('Productos archivados')->send();
                    }),
                ])->label('More actions'),

            ]);
    }
}
