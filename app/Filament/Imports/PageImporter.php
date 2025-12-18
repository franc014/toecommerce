<?php

namespace App\Filament\Imports;

use App\Models\Page;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class PageImporter extends Importer
{
    protected static ?string $model = Page::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')
                ->requiredMapping()
                ->rules(['required', 'max:100']),
            ImportColumn::make('slug')
                ->requiredMapping()
                ->rules(['required', 'max:100']),
            ImportColumn::make('description')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('status')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('metatags')
                ->requiredMapping()
                ->rules(['required'])
                ->castStateUsing(function ($state) {
                    return json_decode($state);
                }),
            ImportColumn::make('route')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('published_at')
                ->rules(['datetime']),
        ];
    }

    public function resolveRecord(): Page
    {
        return Page::firstOrNew([
            'slug' => $this->data['slug'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your page import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}