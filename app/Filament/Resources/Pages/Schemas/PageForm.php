<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Enums\SectionStatus;
use App\Filament\Forms\Components\SharedFields;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class PageForm
{
    use SharedFields;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(8)
            ->components([
                Section::make(__('firesources.main_information'))
                    ->description(__('firesources.main_information_description'))
                    ->collapsible()
                    ->columnSpan(4)
                    ->icon(Heroicon::OutlinedPencil)
                    ->schema([
                        ...self::titleAndSlugFields('route'),
                        Textarea::make('description')
                            ->label(__('firesources.description'))
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('route')
                            ->label(__('firesources.route'))
                            ->required()
                            ->minLength(3)
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),

                    ]),

                Section::make(__('firesources.page_content'))
                    ->label(__('firesources.page_content'))
                    ->description(function (): Htmlable {
                        if (app()->getLocale() === 'en') {
                            return new HtmlString('<p class="text-sm italic">Only <b>active</b> sections are displayed to compose the page.</p>');
                        }

                        return new HtmlString('<p class="text-sm italic">Solo las secciones <b>activas</b> se muestran para formar la página.</p>');
                    })
                    ->collapsible()
                    ->columnSpan(4)
                    ->icon(Heroicon::OutlinedRectangleGroup)
                    ->schema([
                        CheckboxList::make('page_sections')
                            ->label(__('firesources.sections'))
                            ->relationship('sections', 'title', function ($query) {
                                return $query->select(['id', 'title'])
                                    ->where('status', '=', SectionStatus::ACTIVE->value);
                            })
                            ->gridDirection('row')
                            ->searchable()
                            ->searchPrompt(__('firesources.search_for_sections'))
                            ->noSearchResultsMessage('No sections found.'),
                    ]),
                Section::make(__('firesources.meta_tags'))
                    ->collapsible()
                    ->columnSpan(4)
                    ->icon(Heroicon::OutlinedTag)
                    ->schema([
                        self::metatagsField()
                            ->columnSpanFull(),
                    ]),

            ]);
    }
}
