<?php

namespace App\Listeners;

use App\Events\CashOnDeliveryConfirmed;
use App\Mail\CashOnDeliveryConfirmed as CashOnDeliveryConfirmedMailable;
use Illuminate\Support\Facades\Mail;

class SendCashOnDeliveryConfirmationNotification
{
    public function __construct()
    {
        //
    }

    public function handle(CashOnDeliveryConfirmed $event): void
    {
        Mail::to($event->order->user)->send(new CashOnDeliveryConfirmedMailable($event->order));
    }
}
