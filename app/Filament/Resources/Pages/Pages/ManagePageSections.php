<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Exports\PageSectionExporter;
use App\Filament\Imports\PageSectionImporter;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\Sections\SectionResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;

class ManagePageSections extends ManageRelatedRecords
{
    protected static string $resource = PageResource::class;

    protected static string $relationship = 'sections';

    protected static ?string $relatedResource = SectionResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
                ExportAction::make()
                    ->exporter(PageSectionExporter::class),
                ImportAction::make()
                    ->importer(PageSectionImporter::class),

            ]);
    }
}
