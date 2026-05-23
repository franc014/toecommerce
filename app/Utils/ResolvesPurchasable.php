<?php

namespace App\Utils;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ResolvesPurchasable
{
    public function __construct(
        private int $purchasableId,
        private string $purchasableType,
    ) {}

    public function resolve(): Model
    {
        $studlyType = Str::studly($this->purchasableType);

        $purchasableClass = resolve("\\JFA\\ToecommerceCore\\Models\\{$studlyType}");

        return $purchasableClass::published()->findOrFail($this->purchasableId);
    }
}
