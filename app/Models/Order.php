<?php

namespace App\Models;

use App\Casts\Money;
use App\Exceptions\CartAlreadyPaidException;
use App\Exceptions\OrderAlreadyConfirmedException;
use App\Exceptions\PayphoneTransactionErrorException;
use App\Exceptions\PlaceOrderForEmptyCartException;
use App\Traits\MoneyFormat;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory, MoneyFormat;

    protected $appends = ['total_without_taxes_in_dollars', 'total_with_taxes_in_dollars', 'total_computed_taxes_in_dollars', 'total_amount_in_dollars'];

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

    public function hasItems(): bool
    {
        return $this->orderItems()->count() > 0;
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
            ->with('orderItems')
            ->first();

        if ($order) {
            return $order;
        }

        $newOrder = self::create([
            'user_id' => $user->id,
            'cart_id' => $cart->id,
            'code' => (string) Str::ulid(),
            'total_amount' => $cart->total_amount,
            'total_with_taxes' => $cart->total_with_taxes,
            'total_without_taxes' => $cart->total_without_taxes,
            'total_computed_taxes' => $cart->total_computed_taxes,
        ]);

        foreach ($cart->items as $item) {
            $newOrder->orderItems()->create([
                'purchasable_id' => $item->purchasable_id,
                'purchasable_type' => $item->purchasable_type,
                'cart_item_id' => $item->id,
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

        return $newOrder->load('orderItems');
    }

    public function addItem(CartItem $item)
    {
        $this->orderItems()->create([
            'purchasable_id' => $item->purchasable_id,
            'purchasable_type' => $item->purchasable_type,
            'cart_item_id' => $item->id,
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

    public function updateItem(CartItem $cartItem): void
    {
        $item = $this->orderItems()->where('cart_item_id', $cartItem->id)->first();
        $item->quantity = $cartItem->quantity;
        $item->total = $cartItem->total;
        $item->total_with_taxes = $cartItem->total_with_taxes;
        $item->computed_taxes = $cartItem->computed_taxes;
        $item->save();
    }

    public function removeItem(CartItem $cartItem): void
    {

        $item = $this->orderItems()->where('cart_item_id', $cartItem->id)->first();
        $item->delete();
    }

    public function updateOrderTally(): void
    {
        $itemsWithoutTaxes = $this->orderItems->filter(function ($item) {
            return $item->taxes === null || count(json_decode($item->taxes)) === 0;
        });

        $itemsWithTaxes = $this->orderItems->filter(function ($item) {
            return $item->taxes !== null && count(json_decode($item->taxes)) > 0;
        });

        $totalWithoutTaxes = $itemsWithoutTaxes->sum('total');
        $totalWithTaxes = $itemsWithTaxes->sum('total');
        $totalComputedTaxes = $this->orderItems->sum('computed_taxes');

        $this->update([
            'total_without_taxes' => $totalWithoutTaxes,
            'total_with_taxes' => $totalWithTaxes,
            'total_computed_taxes' => $totalComputedTaxes,
            'total_amount' => $totalWithoutTaxes + $totalWithTaxes + $totalComputedTaxes,
        ]);
    }

    public function cancel(): void
    {
        $this->delete();
    }

    public function isConfirmed(): bool
    {
        return $this->paid_at !== null;
    }

    public function confirm(string $payphoneConfirmation)
    {

        $payphoneConfirmation = json_decode($payphoneConfirmation, true);

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

    protected function totalWithoutTaxesInDollars(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->toDollars($this->total_without_taxes)
        );
    }

    protected function totalWithTaxesInDollars(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->toDollars($this->total_with_taxes)
        );
    }

    protected function totalComputedTaxesInDollars(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->toDollars($this->total_computed_taxes)
        );
    }

    protected function totalAmountInDollars(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->toDollars($this->total_amount)
        );
    }
}
