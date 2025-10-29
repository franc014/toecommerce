<?php

namespace App\Filament\Resources\UserInfoEntries\Pages;

use App\Filament\Resources\UserInfoEntries\UserInfoEntryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserInfoEntry extends CreateRecord
{
    protected static string $resource = UserInfoEntryResource::class;
}
