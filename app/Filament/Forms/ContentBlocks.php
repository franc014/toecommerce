<?php

namespace App\Filament\Forms;

use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\CTABlock;
use Filament\Forms\Components\Builder\Block;
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
}
