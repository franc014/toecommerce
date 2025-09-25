<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [

            EditAction::make(),
            DeleteAction::make(),
            /*  Actions\EditAction::make()->form(Product::getForm())->slideOver()->successNotification(function () {
                return Notification::make()
                    ->success()
                    ->color('success')
                    ->title('Producto actualizado')
                    ->body('El producto se ha actualizado correctamente');
            }), */
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return "Producto: {$this->record->title}";
    }

    public function getBreadcrumb(): string
    {
        return 'Detalle';
    }
}
