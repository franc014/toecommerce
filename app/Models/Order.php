<?php

namespace App\Models;

use App\Casts\Money;
use App\Exceptions\CartAlreadyPaidException;
use App\Exceptions\OrderAlreadyConfirmedException;
use App\Exceptions\PayphoneTransactionErrorException;
use App\Exceptions\PlaceOrderForEmptyCartException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function placeFor(User $user, Cart $cart)
    {
        if ($cart->isEmpty()) {
            throw new PlaceOrderForEmptyCartException;
        }

        if ($cart->isPaid()) {
            throw new CartAlreadyPaidException;
        }

        $order = self::where('cart_id', $cart->id)
            ->where('user_id', $user->id)
            ->first();

        if ($order) {
            return $order;
        }

        $order = self::create([
            'user_id' => $user->id,
            'cart_id' => $cart->id,
            'code' => (string) Str::ulid(),
            'total_amount' => $cart->total_amount,
            'total_with_taxes' => $cart->total_with_taxes,
            'total_without_taxes' => $cart->total_without_taxes,
            'total_computed_taxes' => $cart->total_computed_taxes,
        ]);

        // $order->orderItems()->createMany($cart->items->toArray());

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

    public function isConfirmed(): bool
    {
        return $this->paid_at !== null;
    }

    public function confirm(string $payphoneConfirmation)
    {

        $payphoneConfirmation = json_decode($payphoneConfirmation, true);

        // ray($payphoneConfirmation);

        if (Arr::exists($payphoneConfirmation, 'errorCode')) {
            throw new PayphoneTransactionErrorException;
        }
        if ($this->isConfirmed()) {
            throw new OrderAlreadyConfirmedException;
        }
        $this->update([
            'paid_at' => now(),
            'payphone_metadata' => $payphoneConfirmation,
        ]);
    }
}
