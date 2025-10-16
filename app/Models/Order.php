<?php

namespace App\Models;

use App\Casts\Money;
use App\Facades\PayphoneClientTransactionIdGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'total_amount' => Money::class,
            'total_with_taxes' => Money::class,
            'total_without_taxes' => Money::class,
            'total_computed_taxes' => Money::class,
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function placeFor(User $user, Cart $cart)
    {
        $order = self::where('cart_id', $cart->id)
                        ->where('user_id', $user->id)
                        ->first();

        if ($order) {
            return $order;
        }

        $order = self::create([
            'user_id' => $user->id,
            'cart_id' => $cart->id,
            'code' => PayphoneClientTransactionIdGenerator::generate(),
            'total_amount' => $cart->total_amount / 100,
            'total_with_taxes' => $cart->total_with_taxes / 100,
            'total_without_taxes' => $cart->total_without_taxes / 100,
            'total_computed_taxes' => $cart->total_computed_taxes / 100,
        ]);

        //$order->orderItems()->createMany($cart->items->toArray());

        foreach ($cart->items as $item) {
            $order->orderItems()->create([
                'purchasable_id' => $item->purchasable_id,
                'purchasable_type' => $item->purchasable_type,
                'title' => $item->title,
                'slug' => $item->slug,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'taxes' => $item->taxes,
                'total' => $item->total,
                'total_with_taxes' => $item->total_with_taxes,
                'computed_taxes' => $item->computed_taxes,
            ]);
        }
        return $order;
    }

    public function confirm(string $payphoneConfirmation)
    {
        $this->update([
            'paid_at' => now(),
            'payphone_metadata' => json_decode($payphoneConfirmation, true)
        ]);
    }
}
