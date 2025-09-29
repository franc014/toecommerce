<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait MoneyFormat
{



    public function toDollars($amount): string
    {
        return '$'.$amount;
    }

    public function priceInDollars(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->toDollars($this->price)
        );
    }

    public function totalInDollars(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->toDollars($this->total)
        );
    }

    public function totalWithTaxesInDollars(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->toDollars($this->total_with_taxes)
        );
    }
}
