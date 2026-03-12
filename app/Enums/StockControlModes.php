<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StockControlModes: string implements HasColor, HasLabel
{
    case STRICT = 'strict';
    case NONE = 'none';

    public function getColor(): string
    {
        return match ($this) {
            self::STRICT => 'danger',
            self::NONE => 'info',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::STRICT => 'Estricto',
            self::NONE => 'Ninguno',
        };
    }
}
