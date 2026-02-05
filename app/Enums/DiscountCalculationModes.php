<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DiscountCalculationModes: string implements HasColor, HasLabel
{
    case HIGHEST = 'highest';
    case SUM = 'sum';

    public function getColor(): string
    {
        return match ($this) {
            self::HIGHEST => 'success',
            self::SUM => 'info',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::HIGHEST => 'El más alto',
            self::SUM => 'Suma',
        };
    }
}
