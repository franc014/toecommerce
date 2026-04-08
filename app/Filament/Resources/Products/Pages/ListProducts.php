<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('firesources.products');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getBreadcrumb(): ?string
    {
        return __('firesources.list');
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('firesources.all'))
                ->icon(Heroicon::Squares2x2)
                ->badge(fn () => Product::query()->count()),
            'active' => Tab::make(__('firesources.published'))
                ->icon(Heroicon::Eye)
                ->badge(fn () => Product::published()->count())
                ->badgeColor('success')
                ->modifyQueryUsing(function ($query) {
                    return $query->published();
                }),
            'draft' => Tab::make(__('firesources.draft'))
                ->icon(Heroicon::Pencil)
                ->badge(fn () => Product::draft()->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(function ($query) {
                    return $query->draft();
                }),
            'archived' => Tab::make(__('firesources.archived'))
                ->icon(Heroicon::ArchiveBox)
                ->badge(fn () => Product::archived()->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(function ($query) {
                    return $query->archived();
                }),
        ];
    }
}
