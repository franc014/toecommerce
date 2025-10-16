<?php

namespace App\Listeners;

use App\Events\OrderConfirmed;
use App\Mail\OrderConfirmed as OrderConfirmedMailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderConfirmed $event): void
    {
        Mail::to($event->order->user)->send(new OrderConfirmedMailable($event->order));
    }
}
