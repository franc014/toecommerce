<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
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
        return 'Productos';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nuevo producto')->icon(Heroicon::OutlinedPlus),
        ];
    }

    public function getBreadcrumb(): ?string
    {
        return 'Lista';
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todos'),
            'active' => Tab::make('Publicados')->modifyQueryUsing(function ($query) {
                return $query->published();
            }),
            'draft' => Tab::make('Borradores')->modifyQueryUsing(function ($query) {
                return $query->draft();
            }),
            'archived' => Tab::make('Archivados')->modifyQueryUsing(function ($query) {
                return $query->archived();
            }),
        ];
    }
}
