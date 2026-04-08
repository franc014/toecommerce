<?php

namespace App\Listeners;

use App\Events\BankTransferReceiptUploaded;
use App\Mail\BankTransferConfirmed;
use Illuminate\Support\Facades\Mail;

class SendBankTransferConfirmationNotification
{
    public function __construct() {}

    public function handle(BankTransferReceiptUploaded $event): void
    {
        Mail::to($event->order->user)->send(new BankTransferConfirmed($event->order));
    }
}
