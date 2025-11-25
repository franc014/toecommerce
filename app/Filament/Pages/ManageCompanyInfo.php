<?php

namespace App\Filament\Pages;

use App\Settings\CompanySettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageCompanyInfo extends SettingsPage
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static string $settings = CompanySettings::class;

    public static function getNavigationGroup(): UnitEnum|string|null
    {
        return __('firesources.settings');
    }

    public function getTitle(): string
    {
        return __('firesources.company_settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('firesources.company_settings');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('whatsapp')
                    ->required(),
                TextInput::make('address')
                    ->required(),
                TextInput::make('socialMedia')
                    ->required(),
                TextInput::make('workingDays')
                    ->required(),
            ]);
    }
}
