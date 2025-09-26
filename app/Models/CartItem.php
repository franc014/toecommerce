<?php

namespace App\Models;

use App\Casts\Money;
use App\Traits\MoneyFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    /** @use HasFactory<\Database\Factories\CartItemFactory> */
    use HasFactory, MoneyFormat;

    protected function casts(): array
    {
        return [
           'price' => Money::class,
           'quantity' => 'integer',
           'total' => Money::class,
           'total_with_taxes' => Money::class,
           'sizes' => 'array',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function scopeAllByProductInOpenCarts($query, $purchasable_id, $purchasable_type)
    {
        return $query->where('purchasable_id', $purchasable_id)
        ->where('purchasable_type', $purchasable_type)
        ->whereHas('cart', function ($q) {
            $q->where('paid_at', null);
        })
        ->get();
    }


}
