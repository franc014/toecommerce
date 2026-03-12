<?php

namespace App\Filament\Resources\ProductCollections\Pages;

use App\Filament\Resources\ProductCollections\ProductCollectionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProductCollection extends ViewRecord
{
    protected static string $resource = ProductCollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
