<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SectionStatus: string implements HasColor, HasLabel
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::INACTIVE => 'warning',
            self::ACTIVE => 'success',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::INACTIVE => 'INACTIVE',
            self::ACTIVE => 'ACTIVE',
        };
    }

}
