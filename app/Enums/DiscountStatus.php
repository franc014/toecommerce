<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DiscountStatus: string implements HasColor, HasLabel
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SCHEDULED = 'scheduled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ACTIVE => __('firesources.active'),
            self::INACTIVE => __('firesources.inactive'),
            self::SCHEDULED => __('firesources.scheduled'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'gray',
            self::SCHEDULED => 'warning',
        };
    }
}
