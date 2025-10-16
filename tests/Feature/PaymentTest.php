<?php

use App\Facades\PayphonePaymentGateway;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\User;

function confirmation()
{
    $user = User::factory()->create();
    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create([
        'user_id' => $user->id,
    ]);
    test()->cart = $cart;
    $order = Order::placeFor($user, $cart);
    test()->order = $order;

    expect($user->orders)->toHaveCount(1);
    expect($user->orders->first()->id)->toBe($order->id);


    $id = 'tx12345';

    $paymentMeta = [
        'transactionId' => $id,
        'clientTransactionId' => $order->code,
        'storeName' => 'ToEcommerce',
        'email' => 'customer@example.com',
        'gateway' => 'Payphone',
        'lastDigits' => '1234',
    ];

    test()->paymentMeta = $paymentMeta;

    PayphonePaymentGateway::shouldReceive('confirm')->andReturn(json_encode($paymentMeta));

    $response = test()->withCookie('cart', $cart->ui_cart_id)->get(route('payments.confirm', [
        'id' => $id,
        'clientTransactionId' => $order->code
    ]));

    return $response;

    /* ->assertRedirect(route('filament.customer.resources.orders.view', [
        'record' => $order->code,
    ])); */
}

test('can confirm payphone payment', function () {
    $this->withoutExceptionHandling();
    $response = confirmation();
    expect($response->status())->toBe(302);
    expect($response->assertRedirect(route('storefront.products')));

    expect($this->order->fresh()->paid_at)->not()->toBeNull();
    expect($this->cart->fresh()->paid_at)->not()->toBeNull();

    $this->assertDatabaseHas('orders', [
        'payphone_metadata' => json_encode($this->paymentMeta)
    ]);


});
