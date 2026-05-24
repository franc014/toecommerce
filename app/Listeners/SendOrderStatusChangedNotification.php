<?php

namespace App\Listeners;

use App\Mail\OrderStatusChanged;
use Illuminate\Support\Facades\Mail;
use JFA\ToecommerceCore\Events\OrderStatusChanged as OrderStatusChangedEvent;

class SendOrderStatusChangedNotification
{
    /**
     * Handle the event.
     */
    public function handle(OrderStatusChangedEvent $event): void
    {
        $order = $event->order;
        $newStatus = $event->newStatus;
        $oldStatus = $event->oldStatus;

        Mail::to($order->user->email)->send(new OrderStatusChanged($order, $newStatus, $oldStatus));
    }
}
