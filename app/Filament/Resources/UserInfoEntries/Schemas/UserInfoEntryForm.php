<?php

namespace App\Filament\Resources\UserInfoEntries\Schemas;

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Database\Eloquent\Builder;

class UserInfoEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label(__('firesources.customer'))
                    ->relationship('user', 'name', function (Builder $query) {
                        $query->whereHas('roles', fn (Builder $query) => $query->where('name', 'customer'))->get();
                    })
                    ->preload()
                    ->searchable(),
                Radio::make('type')
                    ->label(__('firesources.type'))
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
                    ->label('Email')
                    ->email()
                    ->required(),
                TextInput::make('country')
                    ->label(__('firesources.country'))
                    ->required(),
                TextInput::make('state')
                    ->label(__('firesources.state'))
                    ->required(),
                TextInput::make('city')
                    ->label(__('firesources.city'))
                    ->required(),
                TextInput::make('address')
                    ->label(__('firesources.address'))
                    ->required(),
                TextInput::make('phone')
                    ->label(__('firesources.phone'))
                    ->tel()
                    ->required(),
                TextInput::make('zipcode')
                    ->label(__('firesources.zip_code'))
                    ->required(),

            ]);
    }
}
