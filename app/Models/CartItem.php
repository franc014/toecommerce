<?php

namespace App\Models;

use App\Casts\Money;
use App\Traits\HasProductVariation;
use App\Traits\MoneyFormat;
use Database\Factories\CartItemFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class CartItem extends Model
{
    /** @use HasFactory<CartItemFactory> */
    use HasFactory, HasProductVariation, MoneyFormat;

    protected $with = ['purchasable'];

    protected function casts(): array
    {
        return [
            'price' => Money::class,
            'discounted_price' => Money::class,
            'quantity' => 'integer',
            'total' => Money::class,
            'total_with_taxes' => Money::class,
            'computed_taxes' => Money::class,
            'variation' => 'array',
            'has_discount' => 'boolean',
        ];
    }

    protected static function booted(): void
    {

        static::created(function (CartItem $cartItem) {
            if ($cartItem->cart->hasUnpaidOrder()) {
                $cartItem->cart->order->addItem($cartItem);
            }
        });

        static::updated(function (CartItem $cartItem) {
            if ($cartItem->cart->hasUnpaidOrder()) {
                $cartItem->cart->order->updateItem($cartItem);
            }
        });

        static::saved(function (CartItem $cartItem) {
            $cartItem->cart->load('items')->updateCartTally();
        });

        static::deleted(function (CartItem $cartItem) {
            $cartItem->cart->updateCartTally();
            if ($cartItem->cart->hasUnpaidOrder()) {
                $cartItem->cart->order->updateOrderTally();
                if (! $cartItem->cart->order->hasItems()) {
                    $cartItem->cart->order->cancel();
                }
            }
        });
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function purchasable(): MorphTo
    {
        return $this->morphTo();
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

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => Storage::url($this->image),
        );
    }

    public function discountedPriceInDollars(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->toDollars($this->discounted_price)
        );
    }
}
