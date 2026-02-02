<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait Taxable
{
    public function hasTaxes(): bool
    {
        return $this->taxes->count() >= 1;
    }

    public function priceWithTaxes(): float
    {
        $price = (int) $this->getAttributes()['price'];

        return round(($price * (1 + $this->taxes->sum('percentage') / 100)) / 100, 2);
    }

    public function priceWithTaxesInDollars(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->toDollars($this->priceWithTaxes())
        );
    }

    public function computedTaxes(): float
    {

        if ($this->hasDiscounts()) {
            return $this->discountedPrice() * ($this->taxes->sum('percentage') / 100);
        }

        return $this->price * ($this->taxes->sum('percentage') / 100);
    }

    public function formattedTaxes(): Attribute
    {
        return Attribute::make(
            get: function () {
                $taxes = $this->taxes->map(function ($tax) {
                    return $tax->name.' ('.$tax->percentage.'%)';
                });

                return $taxes->implode(', ');
            }
        );
    }
}
