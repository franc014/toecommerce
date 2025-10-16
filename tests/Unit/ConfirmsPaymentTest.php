<?php

use App\Exceptions\OrderAlreadyConfirmedException;
use App\Exceptions\PayphoneTransactionErrorException;
use App\Facades\PayphonePaymentGateway;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\User;
use App\Utils\ConfirmsPayment;

it('handles payment confirmation', function () {
    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create([
        'user_id' => null
    ]);
    $order = Order::placeFor(User::factory()->create(), $cart);

    $payphoneConfirmation = [
        'id' => '12345',
        'clientTransactionId' => '1234567890',
    ];

    PayphonePaymentGateway::shouldReceive('confirm')->andReturn(json_encode($payphoneConfirmation));

    $confirms = new ConfirmsPayment($cart->ui_cart_id, $payphoneConfirmation);

    $confirms->handle();

    expect($order->fresh()->paid_at)->not()->toBeNull();
    expect($cart->fresh()->paid_at)->not()->toBeNull();

    expect($order->fresh()->payphone_metadata)->not()->toBeNull();

});

it('throws an exception if order has been already paid', function () {

    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create([
        'user_id' => User::factory()
    ]);
    Order::factory()->paid()->create([
        'cart_id' => $cart->id
    ]);

    $payphoneConfirmation = [
        'id' => '12345',
        'clientTransactionId' => '1234567890',
    ];
    PayphonePaymentGateway::shouldReceive('confirm')->andReturn(json_encode($payphoneConfirmation));

    $confirms = new ConfirmsPayment($cart->ui_cart_id, $payphoneConfirmation);

    $this->assertThrows(
        fn () => $confirms->handle(),
        OrderAlreadyConfirmedException::class,
    );
});

test('trying to create an order for a gateway with response error throws an exception', function () {

    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create([
       'user_id' => null
    ]);
    Order::placeFor(User::factory()->create(), $cart);

    $payphoneConfirmation = [
        'id' => '12345',
        'clientTransactionId' => '1234567890',
    ];

    $paymentMeta = [
        'transactionId' => 'tx12345',
        'clientTransactionId' => 'cltx12345',
        'errorCode' => 1,
    ];

    PayphonePaymentGateway::shouldReceive('confirm')->andReturn(json_encode($paymentMeta));

    $confirms = new ConfirmsPayment($cart->ui_cart_id, $payphoneConfirmation);

    $this->assertThrows(
        fn () => $confirms->handle(),
        PayphoneTransactionErrorException::class,
    );



});
