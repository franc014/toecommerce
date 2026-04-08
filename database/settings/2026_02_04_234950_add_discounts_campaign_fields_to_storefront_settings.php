<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('storefront.show_discount_campaign_message', false);
        $this->migrator->add('storefront.discount_campaign_message', '');
    }
};
