<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

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
        return 'Producto';
    }

    public function getContentTabIcon(): BackedEnum|string|null
    {
        return 'icon-box';
    }

    public static function getTabComponent(): Tab
    {
        return Tab::make('Produit')->icon(Heroicon::OutlinedCube);
    }
}
