<?php

namespace App\Filament\Resources\UserInfoEntries\Pages;

use App\Filament\Resources\UserInfoEntries\UserInfoEntryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUserInfoEntry extends EditRecord
{
    protected static string $resource = UserInfoEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
