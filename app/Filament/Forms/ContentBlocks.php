<?php

namespace App\Filament\Forms;

use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\CTABlock;
use App\Models\Product;
use App\Models\ProductCollection;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class ContentBlocks
{
    private static function toolBarButtons(): array
    {
        return [
            ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
            ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
            ['blockquote', 'codeBlock', 'bulletList', 'orderedList', 'details'],
            ['table', 'attachFiles', 'customBlocks'], // The `customBlocks` and `mergeTags` tools are also added here if those features are used.
            ['undo', 'redo'],
        ];
    }

    private static function floatingToolbarButtons(): array
    {
        return [
            'paragraph' => [
                'bold', 'italic', 'underline', 'strike', 'subscript', 'superscript',
            ],
            'heading' => [
                'h1', 'h2', 'h3',
            ],
        ];
    }

    private static function setBlockLabel($state, $type)
    {

        if ($state === null) {
            return ucfirst($type);
        }

        return $type.' - '.Str::limit($state['content'], 50, '...');
    }

    private static function setBlockLabelRich($state, $type)
    {

        if ($state === null) {
            return ucfirst($type);
        }

        $content = $state['content'] ?? '';

        if ($content) {
            return $type.' - '.Str::limit(RichContentRenderer::make($content)->toText(), 50, '...');
        }

        return '';
    }

    public static function heading(): Block
    {
        return Block::make('heading')
            ->schema([

                TextInput::make('content')
                    ->label(__('firesources.content'))
                    ->live(debounce: 500)
                    ->required(),
                Select::make('level')
                    ->label(__('firesources.level'))
                    ->options([
                        'h1' => __('firesources.heading_1'),
                        'h2' => __('firesources.heading_2'),
                        'h3' => __('firesources.heading_3'),
                        'h4' => __('firesources.heading_4'),
                        'h5' => __('firesources.heading_5'),
                        'h6' => __('firesources.heading_6'),
                    ])->required(),
                TextInput::make('handle')
                    ->minLength(3)
                    ->maxLength(10)
                    ->required(),
            ])

            ->label(function (?array $state): string {
                return self::setBlockLabel($state, __('firesources.heading'));
            })
            ->icon(Heroicon::OutlinedBars2);

    }

    public static function paragraph(): Block
    {
        return Block::make('paragraph')
            ->label(__('firesources.paragraph'))
            ->schema([
                Textarea::make('content')
                    ->label(__('firesources.paragraph'))
                    ->maxLength(2048)
                    ->live(debounce: 500)
                    ->required(),
            ])
            ->label(function (?array $state): string {
                return self::setBlockLabel($state, __('firesources.paragraph'));
            })
            ->icon(Heroicon::Bars3BottomLeft);
    }

    public static function richEditor(): Block
    {
        return Block::make('rich-editor')
            ->schema([
                RichEditor::make('content')
                    ->customBlocks([
                        CTABlock::class,
                    ])
                    ->json()
                    ->label(__('firesources.rich_editor'))
                    ->toolbarButtons(self::toolBarButtons())
                    ->live(debounce: 500)
                    ->required(),
            ])
            ->label(function (?array $state): string {
                return self::setBlockLabelRich($state, __('firesources.rich_editor'));
            })
            ->icon(Heroicon::Bars4);
    }

    public static function cta(): Block
    {
        return Block::make('cta')
            ->schema([
                TextInput::make('content')
                    ->label(__('firesources.cta'))
                    ->maxLength(32)
                    ->required(),
                TextInput::make('link')
                    ->label(__('firesources.link'))
                    ->maxLength(2048)
                    ->required(),
            ])
            ->label(function (?array $state): string {
                return self::setBlockLabel($state, __('firesources.cta'));
            })
            ->icon(Heroicon::Link);
    }

    public static function image(): Block
    {
        return Block::make('image')
            ->schema([
                FileUpload::make('image')
                    ->label(__('firesources.image'))
                    ->directory('block_images')
                    ->maxSize(1024 * 3)
                    ->visibility('public')
                    ->image(),
            ])
            ->label(__('firesources.image'))
            ->icon(Heroicon::Photo);

    }

    public static function video(): Block
    {
        return Block::make('video')
            ->schema([
                TextInput::make('title')
                    ->label(__('firesources.title'))
                    ->required()
                    ->maxLength(128),
                Textarea::make('message')
                    ->label(__('firesources.message'))
                    ->maxLength(256),
                TextInput::make('link')
                    ->label(__('firesources.link'))
                    ->required()
                    ->maxLength(2048),
            ])
            ->label(__('firesources.video'))
            ->icon(Heroicon::VideoCamera);

    }

    public static function newProductsChoice(): Block
    {
        return Block::make('new-products')
            ->schema([
                CheckboxList::make('products')
                    ->label(__('firesources.new_products'))
                    ->options(Product::query()->published()->latest()->take(10)->get()->pluck('title', 'id')->toArray())
                    ->searchable(),
            ])
            ->maxItems(1)
            ->label(__('firesources.new_products_choice'))
            ->icon(Heroicon::ViewfinderCircle);
    }

    public static function featuredProduct(): Block
    {
        return Block::make('featured-product')
            ->schema([
                TextInput::make('title')
                    ->label(__('firesources.title'))
                    ->required()
                    ->maxLength(128),
                Textarea::make('message')
                    ->label(__('firesources.message'))
                    ->maxLength(256),

                TextInput::make('cta_label')
                    ->label(__('firesources.cta_label'))
                    ->required()
                    ->maxLength(24),

                Select::make('product')
                    ->label(__('firesources.featured_product'))
                    ->required()
                    ->options(Product::query()->published()->get()->pluck('title', 'id')->toArray())
                    ->searchable(),
            ])
            ->maxItems(1)
            ->label(__('firesources.featured_product'))
            ->icon(Heroicon::Star);
    }

    public static function collections(): Block
    {
        return Block::make('collections')
            ->schema([
                CheckboxList::make('collections')
                    ->label(__('firesources.collections'))
                    ->options(ProductCollection::whereHas('products')->get()->pluck('title', 'id')->toArray()),
            ])
            ->maxItems(1)
            ->label(__('firesources.collections'))
            ->icon(Heroicon::OutlinedRectangleStack);
    }

    public static function feature(): Block
    {
        return Block::make('feature')
            ->schema([
                TextInput::make('title')
                    ->label(__('firesources.title'))
                    ->maxLength(128)
                    ->required(),
                Textarea::make('message')
                    ->label(__('firesources.message'))
                    ->maxLength(256)
                    ->required(),
                FileUpload::make('image')
                    ->label(__('firesources.image'))
                    ->directory('block_images')
                    ->maxSize(1024 * 3)
                    ->visibility('public')
                    ->image(),
            ])
            ->label(function (?array $state): string {
                if ($state === null) {
                    return ucfirst('feature');
                }

                return 'feature-'.Str::limit($state['title'], 50, '...');
            })
            ->icon(Heroicon::Link);
    }
}
