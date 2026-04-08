<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    public function getSubheading(): ?string
    {
        return __('firesources.orders_list_subheading');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getDefaultActiveTab(): string|int
    {
        return 'pending';
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('firesources.all'))
                ->icon(Heroicon::QueueList)
                ->badge(fn () => Order::query()->count()),
            'pending' => Tab::make(OrderStatus::PENDING->getLabel())
                ->icon(Heroicon::Clock)
                ->badge(fn () => Order::query()->where('status', OrderStatus::PENDING)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', OrderStatus::PENDING);
                }),
            'shipping' => Tab::make(OrderStatus::SHIPPING->getLabel())
                ->icon(Heroicon::Truck)
                ->badge(fn () => Order::query()->where('status', OrderStatus::SHIPPING)->count())
                ->badgeColor('primary')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', OrderStatus::SHIPPING);
                }),
            'shipped' => Tab::make(OrderStatus::SHIPPED->getLabel())
                ->icon(Heroicon::CheckCircle)
                ->badge(fn () => Order::query()->where('status', OrderStatus::SHIPPED)->count())
                ->badgeColor('success')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', OrderStatus::SHIPPED);
                }),
            'canceled' => Tab::make(OrderStatus::CANCELED->getLabel())
                ->icon(Heroicon::XCircle)
                ->badge(fn () => Order::query()->where('status', OrderStatus::CANCELED)->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(function ($query) {
                    return $query->where('status', OrderStatus::CANCELED);
                }),
        ];
    }
}
