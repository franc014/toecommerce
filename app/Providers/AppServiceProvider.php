<?php

namespace App\Providers;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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
        Vite::prefetch(concurrency: 3);
        Model::automaticallyEagerLoadRelationships();
        Model::unguard();

        Action::configureUsing(function (Action $action) {
            return $action->slideover()->modalWidth('5xl');
        });

        CreateAction::configureUsing(function (CreateAction $action) {
            return $action->icon(Heroicon::OutlinedPlus);
        });

        DeleteAction::configureUsing(function (DeleteAction $action) {
            return $action->modalWidth('xl')->slideOver(false);
        });
    }
}
