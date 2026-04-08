<?php

namespace App\Filament\Actions;

use App\Models\Discount;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class DiscountsAction
{
    public static function make(): Action
    {
        return Action::make('discounts')
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
            ->fillForm(function (Model $record) {
                return [
                    'discounts' => $record->discounts()->get()->pluck('id'),
                ];
            })
            ->action(function (Model $record, array $data) {
                $record->discounts()->sync($data['discounts']);
            })
            ->after(function () {
                return Notification::make()
                    ->success()
                    ->title(__('firesources.discounts_updated'))->send();
            });
    }
}
