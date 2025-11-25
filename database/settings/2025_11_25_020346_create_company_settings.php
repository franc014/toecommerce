<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('company.name', 'ToEcommerce');
        $this->migrator->add('company.email', 'jfandtec@gmail.com');
        $this->migrator->add('company.phone', '593968741465');
        $this->migrator->add('company.whatsapp', '593968741465');
        $this->migrator->add('company.address', 'Quito, Ecuador');
        $this->migrator->add('company.socialMedia', []);
        $this->migrator->add('company.workingDays', []);
    }
};
