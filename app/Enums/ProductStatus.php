<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProductStatus: string implements HasColor, HasLabel
{
    case DRAFT = 'borrador';
    case ACTIVE = 'publicado';
    case ARCHIVED = 'archivado';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DRAFT => 'warning',
            self::ACTIVE => 'success',
            self::ARCHIVED => 'gray',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAFT => strtoupper(__('firesources.draft')),
            self::ACTIVE => strtoupper(__('firesources.published')),
            self::ARCHIVED => strtoupper(__('firesources.archived')),
        };
    }
}
