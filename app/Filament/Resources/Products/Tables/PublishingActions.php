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
                ->label(__('firesources.duplicate'))
                ->successNotificationTitle('Producto duplicado')->size('xs'),

            Action::make('publish')
                ->label(__('firesources.publish'))
                ->action(function (Model $product) {
                    $product->publish();
                })->icon(Heroicon::OutlinedArrowUpOnSquare)
                ->visible(function (Model $product) {
                    return $product->status === ProductStatus::DRAFT || $product->status === ProductStatus::ARCHIVED;
                })
                ->color('success')
                ->after(function () {
                    return Notification::make()
                        ->success()
                        ->title(__('firesources.published_product'))->send();
                }),

            Action::make('unpublish')
                ->label(__('firesources.unpublish'))
                ->action(function (Model $product) {
                    $product->unpublish();
                })->icon(Heroicon::OutlinedArrowDownOnSquare)
                ->visible(function (Model $product) {
                    return $product->status === ProductStatus::ACTIVE;
                })
                ->slideover(false)
                ->modalWidth('xl')

                ->color('warning')
                ->after(function () {
                    return Notification::make()
                        ->success()
                        ->title(__('firesources.unpublished_product'))->send();
                })->requiresConfirmation(),

            Action::make('archive')->action(function (Model $product) {
                $product->archive();
            })->icon(Heroicon::OutlinedArchiveBoxArrowDown)
                ->label(__('firesources.archive'))
                ->slideover(false)
                ->modalWidth('xl')
                ->color('gray')
                ->visible(function (Model $product) {
                    return $product->status === ProductStatus::ACTIVE || $product->status === ProductStatus::DRAFT;
                })
                ->after(function () {
                    return Notification::make()
                        ->success()
                        ->title(__('firesources.archived_product'))->send();
                })
                ->requiresConfirmation(),
            DeleteAction::make()->label(__('firesources.delete'))->color('danger')->size('xs'),
        ]);
    }

    public static function getBulkPublishingActions(): BulkActionGroup
    {
        return BulkActionGroup::make([

            BulkAction::make('publish')
                ->label(__('firesources.publish'))
                ->action(function (Collection $records) {
                    foreach ($records as $record) {
                        $record->publish();
                    }
                })->icon(Heroicon::OutlinedArrowUpOnSquare)
                ->color('success')
                ->after(function () {
                    return Notification::make()
                        ->success()
                        ->title(__('firesources.published_products'))->send();
                }),

            BulkAction::make('unpublish')->action(function (Collection $records) {
                foreach ($records as $record) {
                    $record->unpublish();
                }
            })->icon(Heroicon::OutlinedArrowDownOnSquare)
                ->color('warning')
                ->label(__('firesources.unpublish'))
                ->requiresConfirmation()
                ->slideover(false)
                ->modalWidth('xl')
                ->after(function () {
                    return Notification::make()
                        ->success()
                        ->title(__('firesources.unpublished_products'))->send();
                }),
            BulkAction::make('archive')->action(function (Collection $records) {
                foreach ($records as $record) {
                    $record->archive();
                }
            })->icon(Heroicon::OutlinedArchiveBoxArrowDown)
                ->color('gray')
                ->label(__('firesources.archive'))
                ->requiresConfirmation()
                ->slideover(false)
                ->modalWidth('xl')
                ->after(function () {
                    return Notification::make()
                        ->success()
                        ->title(__('firesources.archived_products'))->send();
                }),
        ])->label('More actions');
    }
}
