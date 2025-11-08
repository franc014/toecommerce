<?php

namespace App\Filament\Resources\UserInfoEntries\Tables;

use App\Models\UserInfoEntry;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Redirect;

class UserInfoEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label(__('firesources.name'))
                    ->state(function ($record): string {
                        return $record->first_name.' '.$record->last_name;
                    }),

                TextColumn::make('type')->label(__('firesources.type'))->sortable()->searchable()->badge()->color(function ($state) {
                    return match ($state) {
                        'billing' => 'info',
                        'shipping' => 'gray',
                        default => 'danger',
                    };
                }),
                TextColumn::make('email')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label(__('firesources.phone'))
                    ->searchable(),

                ToggleColumn::make('is_main')
                    ->label(__('firesources.is_main'))
                    ->sortable()
                    ->searchable()
                    ->afterStateUpdated(function ($state, $record) {
                        $others = UserInfoEntry::where('id', '!=', $record->id)->where('type', $record->type)->get();
                        if ($state) {

                            foreach ($others as $other) {
                                $other->update([
                                    'is_main' => false,
                                ]);
                            }
                        } else {

                            if (! $others->contains('is_main', true)) {
                                Notification::make()
                                    ->title(__('firesources.couldnt_execute_action'))
                                    ->body(__('firesources.check_another_as_main_entry'))
                                    ->danger()
                                    ->send();
                                $record->update([
                                    'is_main' => true,
                                ]);

                                if (Filament::getCurrentPanel()->getId() === 'customer') {
                                    return Redirect::route('filament.customer.resources.user-info-entries.index');
                                }

                                return Redirect::route('filament.admin.resources.user-info-entries.index');
                            }
                        }
                    }),
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
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                ReplicateAction::make()
                    ->slideOver(false)
                    /* ->modalHeading(function ($record) {
                        return 'Duplicar '.$record->first_name;
                    })
                    ->modalSubmitActionLabel('Duplicar')
                    ->modalCancelActionLabel('Cancelar') */
                    ->modalWidth('xl'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
