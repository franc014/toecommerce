<?php

namespace App\Filament\Pages;

use App\Enums\StockControlModes;
use App\Settings\StorefrontSettings;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageStorefront extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string $settings = StorefrontSettings::class;

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

    public function getTitle(): string
    {
        return __('firesources.storefront_settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('firesources.storefront_settings');
    }
}
