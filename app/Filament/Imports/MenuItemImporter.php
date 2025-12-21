<?php

namespace App\Filament\Imports;

use App\Models\MenuItem;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class MenuItemImporter extends Importer
{
    protected static ?string $model = MenuItem::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('menu_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('slug')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('label')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('url')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('items')
                ->requiredMapping()
                ->castStateUsing(function ($state) {
                    return json_decode($state);
                }),
            ImportColumn::make('order_column')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
        ];
    }

    public function resolveRecord(): MenuItem
    {
        return MenuItem::firstOrNew([
            'slug' => $this->data['slug'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your menu item import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
