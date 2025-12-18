<?php

namespace App\Filament\Resources\Pages\Tables;

use App\Filament\Exports\PageExporter;
use App\Filament\Imports\PageImporter;
use App\Filament\Resources\Pages\PageResource;
use App\Models\Page;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PagesTable
{
    public static function customActions(): array
    {
        return [
            Action::make('publish')
                ->action(function ($record) {
                    $record->publish();
                })
                ->color('success')
                ->icon(Heroicon::OutlinedArrowSmallUp)
                ->hidden(function ($record) {
                    return $record->status->value === 'published';
                }),
            Action::make('unpublish')
                ->action(function ($record) {
                    $record->unpublish();
                })
                ->icon(Heroicon::OutlinedArrowSmallDown)
                ->color('warning')
                ->hidden(function ($record) {
                    return $record->status->value === 'draft';
                }),
            Action::make('manageSections')
                ->label('Manage Sections')
                ->icon(Heroicon::OutlinedRectangleGroup)
                ->color('accent')
                ->visible(function (Page $record) {
                    return $record->hasSections();
                })
                ->url(fn (Page $record): string => PageResource::getUrl('manageSections', ['record' => $record])),
        ];
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->label(__('firesources.title'))
                    ->description(function ($record) {
                        return str()->limit($record->description, 80, '...');
                    })
                    ->wrap()
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('firesources.status'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('sections.title')
                    ->label(__('firesources.sections'))
                    ->badge()
                    ->color('info')
                    ->searchable(),
                TextColumn::make('published_at')
                    ->label(__('firesources.published_at'))
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('firesources.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('firesources.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ...self::customActions(),
                EditAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                ->exporter(PageExporter::class)
                ->columnMappingColumns(3),
                ImportAction::make()
                ->importer(PageImporter::class)
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
