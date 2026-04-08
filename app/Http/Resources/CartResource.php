<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request): array
    {

        return [
            'ui_cart_id' => $this->ui_cart_id,
            'items' => CartItemResource::collection($this->items),
            'cart_aggregation' => [
                'total_without_taxes_in_dollars' => $this->totalWithoutTaxesInDollars,
                'total_with_taxes_in_dollars' => $this->totalWithTaxesInDollars,
                'total_computed_taxes_in_dollars' => $this->totalComputedTaxesInDollars,
                'total_in_dollars' => $this->totalAmountInDollars,
                'items_count' => $this->itemsCount,
            ],
        ];
    }
}
