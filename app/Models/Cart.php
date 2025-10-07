<?php

namespace App\Models;

use App\Exceptions\ProductOutOfStockException;
use App\Traits\MoneyFormat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    /** @use HasFactory<\Database\Factories\CartFactory> */
    use HasFactory, MoneyFormat;

    protected $appends = ['subtotal','subtotal_in_dollars','total_with_taxes','total_with_taxes_in_dollars','items_count'];

    protected function casts(): array
    {
        return [
            'cart_items' => 'array',
            'paid_at' => 'datetime: Y-m-d H:i:s',
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
            $cartItem =  $this->items()->create($data);
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
        $item->save();
    }

    public function removeItem(int $itemId): void
    {
        $item = $this->itemById($itemId);
        $item->delete();
    }

    private function productOutOfStockCheck(array $data):void
    {

        if (AppSettings::isStockControlStrict()) {

            $items = CartItem::allByProductInOpenCarts($data['purchasable_id'], $data['purchasable_type']);

            $totalQuantity = $items->sum('quantity') + $data['quantity'];

            $purchasable = $data['purchasable_type']::find($data['purchasable_id']);

            if ($purchasable && $purchasable->stock - $totalQuantity <= 0) {
                throw new ProductOutOfStockException();
            }
        }
    }

    protected function subtotal(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->items ? $this->items->sum('total') : 0
        );
    }

    protected function subtotalInDollars(): Attribute
    {

        return Attribute::make(
            get: fn () => $this->toDollars($this->subtotal)
        );
    }

    protected function totalWithTaxes(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->items ? $this->items->sum('total_with_taxes') : 0
        );
    }

    protected function totalWithTaxesInDollars(): Attribute
    {
        ray('twt', $this->totalWithTaxes);
        return Attribute::make(
            get: fn () => $this->toDollars($this->totalWithTaxes)
        );
    }

    protected function itemsCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->items ? $this->items->sum('quantity') : 0
        );
    }



}
