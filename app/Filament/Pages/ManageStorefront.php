<?php

namespace App\Filament\Pages;

use App\Enums\StockControlModes;
use App\Settings\StorefrontSettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageStorefront extends SettingsPage
{

    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string $settings = StorefrontSettings::class;

    public static function getNavigationGroup(): UnitEnum|string|null
    {
        return __('firesources.settings');
    }

    public function getTitle(): string
    {
        return __('firesources.storefront_settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('firesources.storefront_settings');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('products_per_page')
                    ->label(__('firesources.products_per_page'))
                    ->numeric()
                    ->required(),
                Select::make('stock_control_mode')
                    ->label(__('firesources.stock_control_mode'))
                    ->options(StockControlModes::class)
                    ->required(),
            ]);
    }


}
