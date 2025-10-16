<?php

namespace App\Models;

use App\Casts\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
           'price' => Money::class,
           'quantity' => 'integer',
           'total' => Money::class,
           'total_with_taxes' => Money::class,
           'computed_taxes' => Money::class,
        ];
    }
}
