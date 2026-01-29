<?php

use App\Enums\DiscountCalculationModes;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('storefront.discount_calculation_mode', DiscountCalculationModes::HIGHEST->value);
    }
};
