<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum NavigationGroup implements HasLabel
{
    case STORE;
    case TAXONOMIES;
    case PURCHASES;
    case ACCESS_CONTROL;

    public function getLabel(): string
    {
        return match ($this) {
            self::TAXONOMIES => __('firesources.taxonomies'),
            self::STORE => __('firesources.store'),
            self::PURCHASES => __('firesources.purchases'),
            self::ACCESS_CONTROL => __('firesources.access_control'),
        };
    }
}
