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
            $cartItem->cart->updateCartTally();
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

    /*  public function toArray(): array
     {
         return [
             'id' => $this->id,
             'purchasable_id' => $this->purchasable_id,
             'purchasable_type' => $this->purchasable_type,
             'title' => $this->title,
             'image' => $this->image,
             'price_in_dollars' => $this->price_in_dollars,
             'total_in_dollars' => $this->total_in_dollars,
             'quantity' => $this->quantity,
             'image_url' => $this->image_url,
             'slug' => $this->slug,
             'price' => $this->price,
             'variation' => $this->variation,
             'taxes' => $this->taxes,
             'total' => $this->total,
             'total_with_taxes' => $this->total_with_taxes,
             'computed_taxes' => $this->computed_taxes,
             'has_discount' => $this->has_discount,
             'discount_percentage' => $this->discount_percentage,
             'discounted_price' => $this->discounted_price,
             'total_with_taxes_in_dollars' => $this->total_with_taxes_in_dollars,
             'computed_taxes_in_dollars' => $this->computed_taxes_in_dollars,
             'image_url' => $this->image_url,
             'formatted_variation' => $this->formatted_variation,
             'discounted_price_in_dollars' => $this->discounted_price_in_dollars,

         ];
     } */
}
