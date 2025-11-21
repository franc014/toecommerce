<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
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
            'all' => Tab::make(__('firesources.all')),
            'active' => Tab::make(__('firesources.published'))->modifyQueryUsing(function ($query) {
                return $query->published();
            }),
            'draft' => Tab::make(__('firesources.draft'))->modifyQueryUsing(function ($query) {
                return $query->draft();
            }),
            'archived' => Tab::make(__('firesources.archived'))->modifyQueryUsing(function ($query) {
                return $query->archived();
            }),
        ];
    }
}
