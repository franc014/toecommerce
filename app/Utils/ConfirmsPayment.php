<?php

namespace App\Utils;

use App\Events\OrderConfirmed;
use App\Facades\PayphonePaymentGateway;
use App\Models\Cart;

class ConfirmsPayment
{
    public function __construct(
        public string $uiCartId,
        public array $payphoneConfirmation
    ) {}

    public function handle()
    {
        $payphoneConfirmation = PayphonePaymentGateway::confirm(...$this->payphoneConfirmation);

        $cart = Cart::byUICartId($this->uiCartId)->firstOrFail();
        $cart->order->confirm($payphoneConfirmation);
        $cart->finish();
        OrderConfirmed::dispatch($cart->order);

    }
}
