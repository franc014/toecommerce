<?php

namespace App\Models;

use App\Enums\StockControlModes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSettings extends Model
{
    /** @use HasFactory<\Database\Factories\AppSettingsFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'stock_control_mode' => StockControlModes::class,
        ];
    }

    public static function isStockControlStrict(): bool
    {
        return self::first() && self::first()->stock_control_mode->value === StockControlModes::STRICT->value;
    }
}
