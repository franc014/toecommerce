<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('firesources.summary'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible()
                    ->headerActions([
                        Action::make('confirm-payment')
                            ->label(__('firesources.confirm_payment'))
                            ->icon(Heroicon::CheckCircle)
                            ->color('success')
                            ->requiresConfirmation()
                            ->slideOver(false)
                            ->modalWidth('xl')
                            ->modalHeading(__('firesources.confirm_payment'))
                            ->modalDescription(__('firesources.confirm_payment_description'))
                            ->modalSubmitActionLabel(__('firesources.confirm'))
                            ->hidden(function ($record) {
                                if (Filament::getCurrentPanel()->getId() !== 'admin') {
                                    return true;
                                }
                                if (! $record->hasItems()) {
                                    return true;
                                }

                                return $record->isConfirmed();
                            })
                            ->action(function ($record) {
                                $record->markAsPaid();
                            })
                            ->successNotificationTitle(__('firesources.order_marked_as_paid')),
                    ])
                    ->schema([
                        TextEntry::make('code')
                            ->label(__('firesources.code')),
                        TextEntry::make('user.name')
                            ->label(__('firesources.customer')),
                        TextEntry::make('total_with_taxes')
                            ->label(__('firesources.total_with_taxes'))
                            ->numeric()
                            ->money('USD'),
                        TextEntry::make('total_without_taxes')
                            ->label(__('firesources.total_without_taxes'))
                            ->numeric()
                            ->money('USD'),
                        TextEntry::make('total_computed_taxes')
                            ->label(__('firesources.total_computed_taxes'))
                            ->numeric()
                            ->money('USD'),
                        TextEntry::make('total_amount')
                            ->label(__('firesources.total_amount'))
                            ->numeric()
                            ->money('USD'),
                        TextEntry::make('created_at')
                            ->label(__('firesources.created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('paid_at')
                            ->label(__('firesources.paid_at'))
                            ->badge()
                            ->color(fn ($record) => $record->isConfirmed() ? 'success' : 'warning')
                            ->dateTime()
                            ->placeholder(__('firesources.not_paid_yet')),
                    ]),
                Section::make(__('firesources.products'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible()

                    ->schema([
                        RepeatableEntry::make('orderItems')
                            ->label(__('firesources.products'))
                            ->columnSpanFull()
                            ->table([
                                TableColumn::make(__('firesources.title')),
                                TableColumn::make(__('firesources.price')),
                                TableColumn::make(__('firesources.quantity')),
                                TableColumn::make(__('firesources.total_amount')),
                                TableColumn::make(__('firesources.total_computed_taxes')),
                                TableColumn::make(__('firesources.total_with_taxes')),

                            ])
                            ->schema([
                                TextEntry::make('title'),
                                TextEntry::make('price')->money('USD'),
                                TextEntry::make('quantity'),
                                TextEntry::make('total')->money('USD'),
                                TextEntry::make('computed_taxes')->money('USD'),
                                TextEntry::make('total_with_taxes')->money('USD'),
                            ]),
                    ])->footer([
                        Action::make('pay')
                            ->label(__('firesources.pay'))
                            ->icon(Heroicon::Banknotes)
                            ->url(fn () => route('storefront.checkout'))
                            ->hidden(function ($record) {
                                if (Filament::getCurrentPanel()->getId() === 'admin') {
                                    return true;
                                }

                                return $record->paid_at !== null;
                            }),
                        Action::make('purchase-more')
                            ->icon(Heroicon::BuildingStorefront)
                            ->label(__('firesources.purchase_more'))
                            ->color('secondary')
                            ->url(fn () => route('storefront.products'))
                            ->hidden(function () {
                                return Filament::getCurrentPanel()->getId() === 'admin';
                            }),
                    ]),
            ]);
    }
}
