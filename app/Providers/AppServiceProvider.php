<?php

namespace App\Providers;

use App\Utils\PayphoneGateway;
use App\Utils\PayphonePayment;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        // TransactionIdGenerator::class => PayphoneTransactionIdGenerator::class,
        PayphonePayment::class => PayphoneGateway::class,
        /* LocalPayment::class => PayphoneGateway::class,
        OrderConfirmationCodeGenerator::class => RandomOrderConfirmationCodeGenerator::class, */
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 6);
        Model::automaticallyEagerLoadRelationships();
        Model::unguard();

        Action::configureUsing(function (Action $action) {
            return $action->slideover()->modalWidth('5xl');
        });

        CreateAction::configureUsing(function (CreateAction $action) {
            return $action->icon(Heroicon::OutlinedPlus)->label(__('firesources.add'));
        });

        EditAction::configureUsing(function (EditAction $action) {
            return $action->icon(Heroicon::PencilSquare)->label(__('firesources.edit'))->modalIcon(Heroicon::PencilSquare);
        });

        ViewAction::configureUsing(function (ViewAction $action) {
            return $action->icon(Heroicon::Eye)
                ->modalIcon(Heroicon::Eye)
                ->label(__('firesources.view'));
        });

        DeleteAction::configureUsing(function (DeleteAction $action) {
            return $action->modalWidth('xl')
                ->modalHeading(function () use ($action) {
                    return __('firesources.delete').($action->getRecordTitle() ? ' '.$action->getRecordTitle() : '');
                })
                ->modalDescription(__('firesources.delete_warning'))
                ->modalCancelActionLabel(__('firesources.cancel'))
                ->modalSubmitActionLabel(__('firesources.delete'))
                ->slideOver(false)
                ->icon(Heroicon::Trash)
                ->label(__('firesources.delete'));

        });

        ReplicateAction::configureUsing(function (ReplicateAction $action) {
            return $action->modalWidth('xl')
                ->slideOver(false);
        });

        ExportAction::configureUsing(function (ExportAction $action) {
            return $action
                ->icon(Heroicon::ArrowUpRight)
                ->label(__('firesources.export'));
        });

        ImportAction::configureUsing(function (ImportAction $action) {
            return $action
                ->icon(Heroicon::ArrowDownLeft)
                ->label(__('firesources.import'));
        });

        DeleteBulkAction::configureUsing(function (DeleteBulkAction $action) {
            return $action
                ->modalWidth('xl')
                ->slideOver(false);
        });

    }
}
