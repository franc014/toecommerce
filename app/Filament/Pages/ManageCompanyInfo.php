<?php

namespace App\Filament\Pages;

use App\Settings\CompanySettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Cache;
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
                    ->label(__('firesources.name'))
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->label(__('firesources.phone'))
                    ->tel()
                    ->required(),
                TextInput::make('whatsapp')
                    ->required(),
                TextInput::make('address')
                    ->label(__('firesources.address'))
                    ->columnSpanFull()
                    ->required(),
                KeyValue::make('socialMedia')
                    ->label(__('firesources.social_media'))
                    ->keyLabel(__('firesources.name'))
                    ->valueLabel(__('firesources.link'))
                    ->keyPlaceholder(__('firesources.social_media_name'))
                    ->valuePlaceholder(__('firesources.social_media_link')),

                KeyValue::make('workingDays')
                    ->label(__('firesources.working_days'))
                    ->keyLabel(__('firesources.day'))
                    ->valueLabel(__('firesources.hours'))
                    ->keyPlaceholder(__('firesources.monday'))
                    ->valuePlaceholder('09:00 - 8:00'),

            ]);
    }

    protected function afterSave(): void
    {
        // Clear your Laravel cache
        Cache::forget('settings.company');
    }
}
