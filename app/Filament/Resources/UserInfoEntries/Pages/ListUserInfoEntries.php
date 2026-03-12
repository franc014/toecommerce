<?php

namespace App\Filament\Resources\UserInfoEntries\Pages;

use App\Filament\Resources\UserInfoEntries\UserInfoEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserInfoEntries extends ListRecords
{
    protected static string $resource = UserInfoEntryResource::class;

    public function getSubheading(): ?string
    {
        return __('firesources.info_for_billing_and_shipping');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
