<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray($request): array
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
    }
}
