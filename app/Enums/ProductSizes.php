<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProductSizes: string implements HasLabel
{
    case XS = 'XS';
    case S = 'S';
    case M = 'M';
    case L = 'L';
    case XL = 'XL';
    case XXL = 'XXL';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
