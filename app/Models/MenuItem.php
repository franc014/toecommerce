<?php

namespace App\Models;

use App\Observers\MenuItemObserver;
use Database\Factories\MenuItemFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

#[ObservedBy(MenuItemObserver::class)]
class MenuItem extends Model implements Sortable
{
    /** @use HasFactory<MenuItemFactory> */
    use HasFactory,SortableTrait;

    protected function casts(): array
    {
        return [
            'items' => 'array',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
