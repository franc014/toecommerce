<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Schemas\Schema;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([


            Section::make('Resumen')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('code')
                    ->label('Código'),
                TextEntry::make('user.name')
                    ->label('Cliente'),
                TextEntry::make('total_with_taxes')
                    ->label('Total, productos con impuestos')
                    ->numeric()
                    ->money('USD'),
                TextEntry::make('total_without_taxes')
                    ->label('Total, productos sin impuestos')
                    ->numeric()
                    ->money('USD'),
                TextEntry::make('total_computed_taxes')
                    ->label('Impuestos calculados')
                    ->numeric()
                    ->money('USD'),
                TextEntry::make('total_amount')
                    ->label('Monto total')
                    ->numeric()
                    ->money('USD'),
                TextEntry::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('paid_at')
                    ->label('Fecha de pago')
                    ->dateTime()
                    ->placeholder('-'),
                ]),
                 Section::make('Productos')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                    RepeatableEntry::make('orderItems')
                    ->label('Productos')
                    ->columnSpanFull()
                    ->table([
                        TableColumn::make('Título'),
                        TableColumn::make('Precio'),
                        TableColumn::make('Cantidad'),
                        TableColumn::make('Subtotal'),
                        TableColumn::make('Impuestos calculados'),
                        TableColumn::make('Total con impuestos'),

                    ])
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('price')->money('USD'),
                        TextEntry::make('quantity'),
                        TextEntry::make('total')->money('USD'),
                        TextEntry::make('computed_taxes')->money('USD'),
                        TextEntry::make('total_with_taxes')->money('USD'),
                    ])
                ]),






            ]);
    }
}
