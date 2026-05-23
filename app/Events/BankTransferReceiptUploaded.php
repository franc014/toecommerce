<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JFA\ToecommerceCore\Models\Order;

class BankTransferReceiptUploaded
{
    use Dispatchable, SerializesModels;

    public function __construct(public Order $order) {}
}
