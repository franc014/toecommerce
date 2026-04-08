<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Menu extends Model
{
    use HasFactory;

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public static function byName($name = 'main'): ?Menu
    {
        $cacheKey = 'menu.id.'.$name;

        $menuId = Cache::remember($cacheKey, now()->addHour(), function () use ($name) {
            return self::where('slug', $name)->value('id');
        });

        if ($menuId === null) {
            return null;
        }

        return self::with(['items' => function ($query) {
            $query->orderBy('order_column');
        }])->find($menuId);
    }
}
