<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'user_id' => $this->user_id,
            'cart_id' => $this->cart_id,
            'total_amount' => $this->total_amount,
            'total_with_taxes' => $this->total_with_taxes,
            'total_without_taxes' => $this->total_without_taxes,
            'total_computed_taxes' => $this->total_computed_taxes,
            'paid_at' => $this->paid_at,
            'payphone_metadata' => $this->payphone_metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'total_with_taxes_in_dollars' => $this->totalWithTaxesInDollars,
            'total_without_taxes_in_dollars' => $this->totalWithoutTaxesInDollars,
            'total_computed_taxes_in_dollars' => $this->totalComputedTaxesInDollars,
            'total_amount_in_dollars' => $this->totalAmountInDollars,
            'order_items' => $this->relationLoaded('orderItems') ? OrderItemResource::collection($this->orderItems) : null,
        ];
    }
}
