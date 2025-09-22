<?php
namespace App\Filament\Resources\Products\Tables;

use App\Enums\ProductStatus;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ReplicateAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PublishingActions
{
    public static function getPublishingActions(): ActionGroup
    {
        return ActionGroup::make([
                    ReplicateAction::make()
                        ->label('Duplicar')
                        ->successNotificationTitle('Producto duplicado')->size('xs'),

                    Action::make('publish')->action(function (Model $product) {
                        $product->publish();
                    })->icon(Heroicon::OutlinedArrowUpOnSquare)
                        ->visible(function (Model $product) {
                            return $product->status === ProductStatus::DRAFT || $product->status === ProductStatus::ARCHIVED;
                        })
                        ->label('Publicar')
                        ->color('success')
                        ->after(function () {
                            return Notification::make()
                                ->success()
                                ->title('Producto publicado')->send();
                        }),

                    Action::make('unpublish')->action(function (Model $product) {
                        $product->unpublish();
                    })->icon(Heroicon::OutlinedArrowDownOnSquare)
                        ->visible(function (Model $product) {
                            return $product->status === ProductStatus::ACTIVE;
                        })
                        ->slideover(false)
                        ->modalWidth('xl')
                        ->label('Borrador')
                        ->color('warning')
                        ->after(function () {
                            return Notification::make()
                                ->success()
                                ->title('Producto despublicado')->send();
                        })->requiresConfirmation(),

                    Action::make('archive')->action(function (Model $product) {
                        $product->archive();
                    })->icon(Heroicon::OutlinedArchiveBoxArrowDown)
                        ->label('Archivar')
                        ->slideover(false)
                        ->modalWidth('xl')
                        ->color('gray')
                        ->visible(function (Model $product) {
                            return $product->status === ProductStatus::ACTIVE || $product->status === ProductStatus::DRAFT;
                        })
                        ->after(function () {
                            return Notification::make()
                                ->success()
                                ->title('Producto archivado')->send();
                        })
                        ->requiresConfirmation(),
                   DeleteAction::make()->label('Eliminar')->color('danger')->size('xs'),
        ]);
    }

    public static function getBulkPublishingActions(): BulkActionGroup
    {
        return BulkActionGroup::make([

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
                    })->icon(Heroicon::OutlinedArrowDownOnSquare)
                      ->color('warning')
                      ->label('Borrador')
                      ->requiresConfirmation()
                      ->slideover(false)
                      ->modalWidth('xl')
                      ->after(function () {
                          return Notification::make()
                              ->success()
                              ->title('Productos despublicados')->send();
                      }),
                    BulkAction::make('archive')->action(function (Collection $records) {
                        foreach ($records as $record) {
                            $record->archive();
                        }
                    })->icon(Heroicon::OutlinedArchiveBoxArrowDown)
                      ->color('gray')
                      ->label('Archivar')
                      ->requiresConfirmation()
                      ->slideover(false)
                      ->modalWidth('xl')
                      ->after(function () {
                          return Notification::make()
                              ->success()
                              ->title('Productos archivados')->send();
                      }),
                ])->label('More actions');
    }
}
