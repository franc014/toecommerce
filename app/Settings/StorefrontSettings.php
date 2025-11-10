<?php

namespace App\Settings;

use App\Enums\StockControlModes;
use Spatie\LaravelSettings\Settings;

class StorefrontSettings extends Settings
{

    public int $products_per_page;
    public string $stock_control_mode;


    public static function group(): string
    {
        return 'storefront';
    }

    public function isAppInStrictMode()
    {
        return $this->stock_control_mode === StockControlModes::STRICT->value;
    }
}
