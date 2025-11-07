<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->columnSpanFull()
                    ->persistTab()
                    ->tabs([
                        Tab::make(__('firesources.general_info'))
                            ->columns(2)
                            ->icon(Heroicon::OutlinedCube)
                            ->schema([
                                TextEntry::make('title')
                                          ->label(__('firesources.title')),
                                TextEntry::make('slug'),
                                TextEntry::make('description')
                                          ->label(__('firesources.description')),
                                TextEntry::make('status')
                                    ->label(__('firesources.status'))
                                    ->badge(),
                            ]),
                        Tab::make(__('firesources.taxonomies'))
                            ->icon(Heroicon::OutlinedTag)
                            ->schema([
                                TextEntry::make('tags.name')
                                    ->label(__('firesources.tags'))
                                    ->placeholder(__('firesources.no_tags_message'))->badge()->color('primary'),
                                TextEntry::make('categories.title')->label(__('firesources.categories'))->badge()->placeholder(__('firesources.no_categories_message'))->color('primary'),
                                TextEntry::make('productCollections.title')->label(__('firesources.collections'))->placeholder(__('firesources.no_collections_message'))->badge(),
                            ]),
                        Tab::make(__('firesources.price_stock_taxes'))
                            ->icon(Heroicon::OutlinedCurrencyDollar)
                            ->columns(2)
                            ->schema([
                                TextEntry::make('price')->label(__('firesources.price'))->money('USD'),
                                TextEntry::make('taxes.name')
                                    ->placeholder(__('firesources.no_taxes_message'))
                                    ->label(__('firesources.taxes'))->badge()->color('primary'),
                                TextEntry::make('stock'),
                                TextEntry::make('stock_threshold_for_customers')
                                    ->label(__('firesources.stock_threshold_for_customers')),
                                TextEntry::make('sku'),
                            ]),

                         Tab::make(__('firesources.variant_options'))
                            ->icon(Heroicon::OutlinedSwatch)

                            ->schema([
                                RepeatableEntry::make('variant_options')
                                    ->label(__('firesources.variant_options'))
                                    ->grid(2)
                                    ->schema([
                                        TextEntry::make('name')->label(__('firesources.name')),
                                        RepeatableEntry::make('values')
                                        ->grid(2)
                                        ->label(__('firesources.variant_option_values'))
                                        ->schema([
                                            TextEntry::make('value')->label(__('firesources.value')),
                                        ])
                                ])
                            ]),



                        Tab::make(__('firesources.images'))
                            ->icon(Heroicon::OutlinedPhoto)
                            ->schema([
                                SpatieMediaLibraryImageEntry::make('product_images')->label('')
                                    ->label(__('firesources.images'))
                                    ->placeholder(__('firesources.no_photos_message'))
                                    ->conversion('thumb')
                                    ->collection('product-images'),
                            ]),
                        ]),

            ]);
    }
}
