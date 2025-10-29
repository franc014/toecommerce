<?php

namespace App\Filament\Resources\UserInfoEntries\Pages;

use App\Filament\Resources\UserInfoEntries\UserInfoEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserInfoEntries extends ListRecords
{
    protected static string $resource = UserInfoEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
