<?php

namespace App\Models;

use App\Casts\Money;
use App\Exceptions\ProductOutOfStockException;
use App\Traits\MoneyFormat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cart extends Model
{
    /** @use HasFactory<\Database\Factories\CartFactory> */
    use HasFactory, MoneyFormat;

    protected $appends = ['total_without_taxes_in_dollars', 'total_with_taxes_in_dollars', 'items_count', 'total_computed_taxes_in_dollars', 'total_amount_in_dollars'];

    protected function casts(): array
    {
        return [
            'cart_items' => 'array',
            'paid_at' => 'datetime: Y-m-d H:i:s',
            'total_without_taxes' => Money::class,
            'total_with_taxes' => Money::class,
            'total_computed_taxes' => Money::class,
            'total_amount' => Money::class,

        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function scopeByUICartId($query, $UICartId): Builder
    {
        return $query->where('ui_cart_id', $UICartId);
    }

    public function itemById(int $id): ?CartItem
    {
        return $this->items->findOrFail($id);
    }

    public function hasItem(string $slug): bool
    {
        return $this->items->contains('slug', $slug);
    }

    public function hasItems(): bool
    {
        return $this->items->count() > 0;
    }

    public function isEmpty(): bool
    {
        return ! $this->hasItems();
    }

    public function getItemByPurchasableId(int $purchasableId): ?CartItem
    {
        return $this->items->where('purchasable_id', $purchasableId)->first();
    }

    public function addOrUpdateItem(array $data): CartItem
    {

        $this->productOutOfStockCheck($data);

        $cartItem = $this->getItemByPurchasableId($data['purchasable_id']);

        if ($cartItem) {
            $this->updateItem($cartItem->id, $data['quantity']);
        } else {
            $cartItem = $this->items()->create($data);
        }

        return $cartItem;
    }

    public function updateItem(int $itemId, $quantity): void
    {
        $item = $this->itemById($itemId);
        $taxes = json_decode($item->taxes);

        $totalTaxes = collect($taxes)->sum('percentage');

        $item->quantity = $quantity;
        $item->total = $item->price * $item->quantity;
        $item->total_with_taxes = $quantity * $item->price * (1 + $totalTaxes / 100);
        $item->computed_taxes = $item->total_with_taxes - $item->total;

        $item->save();

    }

    public function removeItem(int $itemId): void
    {
        $item = $this->itemById($itemId);
        $item->delete();
    }

    public function updateCartTally(): void
    {
        $itemsWithoutTaxes = $this->items->filter(function ($item) {
            return $item->taxes === null || count(json_decode($item->taxes)) === 0;
        });

        $itemsWithTaxes = $this->items->filter(function ($item) {
            return $item->taxes !== null && count(json_decode($item->taxes)) > 0;
        });

        $totalWithoutTaxes = $itemsWithoutTaxes->sum('total');
        $totalWithTaxes = $itemsWithTaxes->sum('total');
        $totalComputedTaxes = $this->items->sum('computed_taxes');

        $this->update([
            'total_without_taxes' => $totalWithoutTaxes,
            'total_with_taxes' => $totalWithTaxes,
            'total_computed_taxes' => $totalComputedTaxes,
            'total_amount' => $totalWithoutTaxes + $totalWithTaxes + $totalComputedTaxes
        ]);
    }

    private function productOutOfStockCheck(array $data): void
    {

        if (AppSettings::isStockControlStrict()) {

            $items = CartItem::allByProductInOpenCarts($data['purchasable_id'], $data['purchasable_type']);

            $totalQuantity = $items->sum('quantity') + $data['quantity'];

            $purchasable = $data['purchasable_type']::find($data['purchasable_id']);

            if ($purchasable && $purchasable->stock - $totalQuantity <= 0) {
                throw new ProductOutOfStockException;
            }
        }
    }

    /* protected function totalWithTaxes(): Attribute
    {
        return Attribute::make(
            get: function () {
                $itemsWithTaxes = $this->items->filter(function ($item) {
                    return $item->taxes !== null && count(json_decode($item->taxes)) > 0;
                });

                return $itemsWithTaxes->sum('total') * 100;
            }
        );
    } */

    /* protected function totalWithoutTaxes(): Attribute
    {

        return Attribute::make(
            get: function () {
                $itemsWithoutTaxes = $this->items->filter(function ($item) {
                    return $item->taxes === null || count(json_decode($item->taxes)) === 0;
                });

                return $itemsWithoutTaxes->sum('total') * 100;
            }
        );
    } */

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

    /* protected function totalComputedTaxes(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->items ? $this->items->sum('computed_taxes') * 100 : 0
        );
    } */



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

    protected function itemsCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->items ? $this->items->sum('quantity') : 0
        );
    }

    public function empty(): int
    {
        return $this->items()->delete();
    }

    public function assingUser(User $user): void
    {
        $this->user_id = $user->id;
        $this->save();
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }

    public function finish(): void
    {
        $this->paid_at = now();
        $this->save();
    }

    public function isPaid(): bool
    {
        return $this->paid_at !== null;
    }
}
