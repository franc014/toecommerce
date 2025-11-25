<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class MenuItem extends Model implements Sortable
{
    /** @use HasFactory<\Database\Factories\MenuItemFactory> */
    use HasFactory,SortableTrait;

    protected function casts(): array
    {
        return [
            'items' => 'array',
        ];
    }
}
