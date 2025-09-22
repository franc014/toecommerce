<?php

namespace App\Traits;

use App\Enums\NavigationGroup;
use UnitEnum;

trait StoreNavigationGroup
{
    public static function getNavigationGroup(): UnitEnum | string | null
    {
        return NavigationGroup::STORE;
    }
}
