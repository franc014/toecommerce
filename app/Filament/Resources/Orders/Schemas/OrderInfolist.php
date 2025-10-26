<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('cart_id')
                    ->numeric(),
                TextEntry::make('code'),
                TextEntry::make('total_amount')
                    ->numeric(),
                TextEntry::make('total_with_taxes')
                    ->numeric(),
                TextEntry::make('total_without_taxes')
                    ->numeric(),
                TextEntry::make('total_computed_taxes')
                    ->numeric(),
                TextEntry::make('paid_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
