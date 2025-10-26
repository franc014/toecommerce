<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('cart_id')
                    ->required()
                    ->numeric(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('total_with_taxes')
                    ->required()
                    ->numeric(),
                TextInput::make('total_without_taxes')
                    ->required()
                    ->numeric(),
                TextInput::make('total_computed_taxes')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('paid_at'),
                TextInput::make('payphone_metadata')
                    ->tel(),
            ]);
    }
}
