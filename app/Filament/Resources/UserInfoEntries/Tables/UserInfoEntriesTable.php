<?php

namespace App\Filament\Resources\UserInfoEntries\Tables;

use App\Models\UserInfoEntry;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
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
                    ->state(function ($record): string {
                        return $record->first_name.' '.$record->last_name;
                    }),

                TextColumn::make('type')->label('Tipo')->sortable()->searchable()->badge()->color(function ($state) {
                    return match ($state) {
                        'billing' => 'info',
                        'shipping' => 'gray',
                        default => 'danger',
                    };
                }),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),

                TextColumn::make('phone')
                    ->searchable(),

                ToggleColumn::make('is_main')->label('Principal')
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
                                    ->title('No se puede desmarcar la información principal sin marcar otra')
                                    ->body('Por favor, seleccione o registra otra información como principal antes de desmarcar esta.')
                                    ->danger()
                                    ->send();
                                $record->update([
                                    'is_main' => true,
                                ]);

                                return Redirect::route('filament.customer.resources.user-info-entries.index');
                            }
                        }
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
                    ->label('Duplicar')
                    ->slideOver(false)
                    ->modalHeading(function ($record) {
                        return 'Duplicar '.$record->first_name;
                    })
                    ->modalSubmitActionLabel('Duplicar')
                    ->modalCancelActionLabel('Cancelar')
                    ->modalWidth('xl'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
