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
                    ->label(__('firesources.type'))
                    ->columnSpanFull()
                    ->options([
                        'billing' => __('firesources.billing'),
                        'shipping' => __('firesources.shipping'),
                    ])
                    ->descriptions([
                        'billing' => __('firesources.billing_info'),
                        'shipping' => __('firesources.shipping_info'),
                    ])
                    ->inline()
                    ->inlineLabel(false)
                    ->required(),
                TextInput::make('first_name')
                    ->label(__('firesources.first_name'))
                    ->required(),
                TextInput::make('last_name')
                    ->label(__('firesources.last_name'))
                    ->required(),

                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('country')
                    ->label(__('firesources.country'))
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
