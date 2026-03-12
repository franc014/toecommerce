<?php

namespace App\Observers;

use App\Models\MenuItem;
use Illuminate\Support\Facades\Cache;

class MenuItemObserver
{
    public function saved(MenuItem $menuItem): void
    {
        ray($menuItem);
        $this->clearMenuCache($menuItem);
    }

    public function deleted(MenuItem $menuItem): void
    {
        $this->clearMenuCache($menuItem);
    }

    private function clearMenuCache(MenuItem $menuItem): void
    {
        if ($menuItem->menu) {
            Cache::forget('menu.'.$menuItem->menu->slug);
        }
    }
}
