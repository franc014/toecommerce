<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    public function getBreadcrumb(): string
    {
        return __('firesources.edit');
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    public function getContentTabLabel(): ?string
    {
        return __('firesources.product');
    }

    public function getContentTabIcon(): BackedEnum|string|null
    {
        return 'icon-box';
    }

    public function getTitle(): string|Htmlable
    {
        return __('firesources.edit').' '.$this->record->title;
    }
}
