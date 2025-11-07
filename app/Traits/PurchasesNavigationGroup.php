<?php

namespace App\Traits;

use App\Enums\NavigationGroup;
use UnitEnum;

trait PurchasesNavigationGroup
{
    public static function getNavigationGroup(): UnitEnum|string|null
    {
        return NavigationGroup::PURCHASES;
    }
}
