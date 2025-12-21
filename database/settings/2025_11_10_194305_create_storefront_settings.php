<?php

use App\Enums\StockControlModes;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('storefront.products_per_page', 5);
        $this->migrator->add('storefront.stock_control_mode', StockControlModes::NONE->value);
    }
};
