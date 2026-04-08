<?php

namespace App\Models;

use App\Casts\Money;
use App\Traits\MoneyFormat;
use Database\Factories\OrderItemFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    /** @use HasFactory<OrderItemFactory> */
    use HasFactory, MoneyFormat;

    protected function casts(): array
    {
        return [
            'price' => Money::class,
            'quantity' => 'integer',
            'discounted_price' => Money::class,
            'total' => Money::class,
            'total_with_taxes' => Money::class,
            'computed_taxes' => Money::class,
            'has_discount' => 'boolean',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    protected static function booted(): void
    {
        static::saved(function (OrderItem $orderItem) {
            $orderItem->order->updateOrderTally();
        });
    }

    public function discountedPriceInDollars(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->toDollars($this->discounted_price)
        );
    }
}
