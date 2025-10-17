<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasProductVariation
{
    public function formattedVariation(): Attribute
    {
        $variation = collect($this->variation);

        $formattedVariation = $variation->map(function ($value, $key) {
            return $key . ': ' . $value;
        })->implode(', ');

        return Attribute::make(
            get: fn () => $formattedVariation
        );
    }
}
