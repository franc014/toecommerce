<?php

namespace App\Utils;

use App\Events\OrderConfirmed;
use App\Facades\PayphonePaymentGateway;
use App\Models\Cart;
use App\Models\Order;

class ConfirmsPayment
{
    public function __construct(
        public string $uiCartId,
        public array $payphoneConfirmation
    ) {}

    public function handle(): Order
    {
        $payphoneConfirmation = PayphonePaymentGateway::confirm(...$this->payphoneConfirmation);

        $cart = Cart::byUICartId($this->uiCartId)->firstOrFail();
        $order = $cart->order;
        $order->confirm($payphoneConfirmation);
        $cart->finish();
        OrderConfirmed::dispatch($cart->order);

        return $order;
    }
}
