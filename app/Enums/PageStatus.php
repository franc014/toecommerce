<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PageStatus: string implements HasColor, HasLabel
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';

    public function label(): string
    {
        return match ($this) {
            self::PUBLISHED => 'Published',
            self::DRAFT => 'Draft',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PUBLISHED => 'success',
            self::DRAFT => 'warning',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAFT => 'DRAFT',
            self::PUBLISHED => 'PUBLISHED'
        };
    }
}
