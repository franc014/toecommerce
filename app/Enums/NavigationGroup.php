<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum NavigationGroup implements HasLabel
{
    case STORE;
    case TAXONOMIES;
    case PURCHASES;

    public function getLabel(): string
    {
        return match ($this) {
            self::TAXONOMIES => __('Taxonomías'),
            self::STORE => __('Tienda'),
            self::PURCHASES => __('Compras'),
        };
    }
}
