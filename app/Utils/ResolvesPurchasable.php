<?php

namespace App\Utils;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ResolvesPurchasable
{
    public function __construct(
        private int $purchasableId,
        private string $purchasableType,
    ) {
    }

    public function resolve(): Model
    {
        $purchasableClass = resolve("\\App\Models\\".Str::studly($this->purchasableType));
        return $purchasableClass::published()->findOrFail($this->purchasableId);
    }
}
