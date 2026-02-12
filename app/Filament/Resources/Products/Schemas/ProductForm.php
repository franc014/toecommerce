<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\ProductStatus;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\HeroBlock;
use App\Filament\Forms\Components\SharedFields;
use App\Models\Tax;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class ProductForm
{
    use SharedFields;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Tabs::make('Producto')
                    ->columnSpanFull()
                    ->persistTab()
                    ->id('product-tabs')
                    ->columns(2)
                    ->tabs([
                        Tab::make(__('firesources.general_info'))
                            ->icon(Heroicon::OutlinedCube)
                            ->schema(
                                [
                                    ...self::titleAndSlugFields(),
                                    Select::make('status')
                                        ->label(__('firesources.status'))
                                        ->required()
                                        ->default(ProductStatus::DRAFT)
                                        ->enum(ProductStatus::class)
                                        ->options(ProductStatus::class),
                                    Textarea::make('summary')
                                        ->label(__('firesources.summary'))
                                        ->maxLength(2048),
                                    RichEditor::make('description')
                                        ->label(__('firesources.description'))
                                        ->toolbarButtons([
                                            ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link', 'textColor'],
                                            ['h1', 'h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                                            ['blockquote', 'bulletList', 'orderedList', 'details'],
                                            ['table', 'grid', 'gridDelete', 'attachFiles'], // The `customBlocks` and `mergeTags` tools are also added here if those features are used.
                                            ['undo', 'redo', 'clearFormatting'],
                                            ['customBlocks'],
                                        ])
                                        ->customBlocks([
                                            HeroBlock::class,
                                        ])
                                        ->required()
                                        ->json()
                                        ->columnSpanFull(),
                                    TextInput::make('sku')->label('SKU'),
                                ]
                            ),
                        Tab::make(__('firesources.taxonomies'))
                            ->icon(Heroicon::OutlinedTag)->schema([
                                SpatieTagsInput::make('tags')
                                    ->label(__('firesources.tags')),
                                Select::make('product_collections')
                                    ->label(__('firesources.collections'))
                                    ->multiple()
                                    ->relationship('productCollections', 'title'),

                                Select::make('category_id')
                                    ->label(__('firesources.categories'))
                                    ->multiple()
                                    ->relationship('categories', 'title'),
                            ]),
                        Tab::make(__('firesources.price_stock_taxes'))
                            ->icon(Heroicon::OutlinedCurrencyDollar)->schema([
                                TextInput::make('price')
                                    ->label(__('firesources.price'))
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(10000)
                                    ->inputMode('decimal')
                                    ->prefix('$'),

                                TextInput::make('discount')
                                    ->label(__('firesources.discount'))
                                    ->numeric()
                                    ->minValue(0.01)
                                    ->maxValue(100)
                                    ->inputMode('decimal')
                                    ->prefix('%'),

                                CheckboxList::make('taxes')
                                    ->label(__('firesources.taxes'))
                                    ->getOptionLabelFromRecordUsing(fn (Tax $record) => "{$record->name} [{$record->percentage} %]")
                                    ->relationship('taxes', 'name'),

                                TextInput::make('stock')
                                    ->label('Stock')
                                    ->required()
                                    ->numeric()
                                    ->step(1),
                                TextInput::make('stock_threshold_for_customers')
                                    ->label(__('firesources.stock_threshold_for_customers'))
                                    ->numeric()
                                    ->step(1),
                            ]),
                        Tab::make(__('firesources.variant_options'))
                            ->icon(Heroicon::OutlinedSwatch)
                            ->schema([
                                Repeater::make('variant_options')
                                    ->label(__('firesources.variant_options'))
                                    ->collapsible()
                                    ->collapsed()
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                    ->addActionAlignment(Alignment::Start)
                                    ->addActionLabel(__('firesources.add_variant_option'))
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Opción')
                                            ->live(debounce: 500)
                                            ->required(),
                                        Repeater::make('values')
                                            ->collapsible()
                                            ->collapsed()
                                            ->itemLabel(fn (array $state): ?string => $state['value'] ?? null)
                                            ->addActionAlignment(Alignment::Start)
                                            ->label(__('firesources.variant_option_values'))
                                            ->addActionLabel(__('firesources.add_value_variant_option'))
                                            ->schema([
                                                TextInput::make('value')
                                                    ->live(debounce: 500)
                                                    ->label(__('firesources.value'))
                                                    ->required(),
                                            ]),

                                    ]),
                                Action::make('generate_variants')
                                    ->label(__('firesources.generate_variants'))
                                    ->button()
                                    ->visible(function (Model $record) {
                                        return count($record->variant_options) > 0;
                                    })
                                    ->color('primary')
                                    ->icon(Heroicon::OutlinedSwatch)
                                    ->action(function (Model $record) {
                                        $record->generateVariants();
                                    })
                                    ->after(function () {
                                        return Notification::make()
                                            ->success()
                                            ->title(__('firesources.variants_generated'))->send();
                                    }),
                            ]),
                        Tab::make(__('firesources.media'))
                            ->icon(Heroicon::OutlinedPhoto)->schema([
                                TextInput::make('video')
                                    ->label(__('firesources.video_url')),
                                FileUpload::make('main_image')
                                    ->label(__('firesources.main_image'))
                                    ->image()
                                    ->required()
                                    ->maxSize(1024 * 3)
                                    ->visibility('public')
                                    ->columnSpanFull()
                                    ->imageEditor(),

                                SpatieMediaLibraryFileUpload::make('product_images')
                                    ->label(__('firesources.gallery_images'))
                                    ->image()
                                    ->required()
                                    ->maxSize(1024 * 3)
                                    ->minFiles(1)
                                    ->maxFiles(5)
                                    ->multiple()
                                    ->imageEditor()
                                    ->panelLayout('grid')
                                    ->uploadingMessage('Cargando imagenes...')
                                    ->reorderable()
                                    ->manipulations([
                                        'thumb' => ['orientation' => '90', 'width' => 200, 'height' => 200],
                                    ])
                                    ->responsiveImages()
                                    ->columnSpanFull()
                                    ->visibility('public')
                                    ->conversion('thumb')
                                    ->collection('product-images'),

                            ]),
                    ]),

            ]);
    }
}
