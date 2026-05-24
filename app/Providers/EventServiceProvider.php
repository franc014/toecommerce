<?php

namespace App\Providers;

use App\Listeners\SendOrderStatusChangedNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use JFA\ToecommerceCore\Events\OrderStatusChanged;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderStatusChanged::class => [
            SendOrderStatusChangedNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
