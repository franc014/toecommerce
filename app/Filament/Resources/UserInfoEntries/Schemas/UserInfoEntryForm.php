<?php

namespace App\Filament\Resources\UserInfoEntries\Schemas;

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserInfoEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Radio::make('type')
                    ->columnSpanFull()
                    ->label('Tipo')
                    ->options([
                        'billing' => 'Facturación',
                        'shipping' => 'Envío',
                    ])
                    ->descriptions([
                        'billing' => 'Información para facturación',
                        'shipping' => 'Información para envío del producto',
                    ])
                    ->inline()
                    ->inlineLabel(false)
                    ->required(),
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name')
                    ->required(),

                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('country')
                    ->required(),
                TextInput::make('state')
                    ->required(),
                TextInput::make('city')
                    ->required(),
                TextInput::make('address')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('zipcode')
                    ->required(),

            ]);
    }
}
