<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'cart_item_id' => $this->cart_item_id,
            'purchasable_id' => $this->purchasable_id,
            'purchasable_type' => $this->purchasable_type,
            'title' => $this->title,
            'image' => $this->image,
            'slug' => $this->slug,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'taxes' => $this->taxes,
            'total' => $this->total,
            'total_with_taxes' => $this->total_with_taxes,
            'computed_taxes' => $this->computed_taxes,
            'has_discount' => $this->has_discount,
            'discount_percentage' => $this->discount_percentage,
            'discounted_price' => $this->discounted_price,
            'price_in_dollars' => $this->priceInDollars,
            'total_in_dollars' => $this->totalInDollars,
            'total_with_taxes_in_dollars' => $this->totalWithTaxesInDollars,
            'computed_taxes_in_dollars' => $this->computedTaxesInDollars,
            'discounted_price_in_dollars' => $this->discountedPriceInDollars,
        ];
    }
}
