<?php

namespace App\Filament\Actions;

use App\Models\Discount;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;

class BulkDiscountsAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('assignDiscounts')
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
            ->icon(Heroicon::OutlinedTag);
    }
}
