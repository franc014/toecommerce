<?php

use App\Events\OrderConfirmed;
use App\Facades\PayphonePaymentGateway;
use App\Mail\OrderConfirmed as OrderConfirmedMailable;
use App\Models\AppSettings;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

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
        'clientTransactionId' => $order->code,
    ]));

    return $response;

}

test('can confirm payphone payment', function () {

    $this->withoutExceptionHandling();

    $response = confirmation();
    expect($response->status())->toBe(302);
    expect($response->assertRedirect(route('storefront.products')));

    expect($this->order->fresh()->paid_at)->not()->toBeNull();
    expect($this->cart->fresh()->paid_at)->not()->toBeNull();

    $this->assertDatabaseHas('orders', [
        'payphone_metadata' => json_encode($this->paymentMeta),
    ]);
});

test('an event is emitted after payment is confirmed', function () {
    Event::fake();
    confirmation();
    Event::assertDispatched(OrderConfirmed::class);
});

function assertForStockTracking()
{

    $productA = Product::factory()->create(
        [
            'stock' => 4,
        ]
    );

    $productB = Product::factory()->create(
        [
            'stock' => 5,
        ]
    );

    $user = User::factory()->create();
    $cart = Cart::factory()->create([
        'user_id' => $user->id,
    ]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'purchasable_id' => $productA->id,
        'quantity' => 2,
    ]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'purchasable_id' => $productB->id,
        'quantity' => 1,
    ]);

    test()->cart = $cart;

    $order = Order::placeFor($user, $cart);

    test()->order = $order;

    $id = 'tx12345';
    $clientTransactionId = 'cltx12345';

    $paymentMeta = [
        'transactionId' => $id,
        'clientTransactionId' => $clientTransactionId,
        'storeName' => 'ToEcommerce',
        'email' => 'customer@example.com',
        'gateway' => 'Payphone',
        'lastDigits' => '1234',
    ];

    test()->paymentMeta = $paymentMeta;

    PayphonePaymentGateway::shouldReceive('confirm')->andReturn(json_encode($paymentMeta));

    test()->withCookie('cart', $cart->ui_cart_id)->get(route('payments.confirm', [
        'id' => $id,
        'clientTransactionId' => $clientTransactionId,
    ]));

    return [$productA, $productB];

}

test('for every product in order its stock is reduced after payment is confirmed in strick mode', function () {

    AppSettings::factory()->create([
        'stock_control_mode' => 'strict',
    ]);

    [$productA, $productB] = assertForStockTracking();

    $this->assertDatabaseHas('products', [
        'id' => $productA->id,
        'stock' => 2,
    ]);
    $this->assertDatabaseHas('products', [
        'id' => $productB->id,
        'stock' => 4,
    ]);

});

test('for every product in order its stock is not reduced after payment is confirmed in non strick mode', function () {

    AppSettings::factory()->create([
        'stock_control_mode' => 'none',
    ]);

    [$productA, $productB] = assertForStockTracking();

    $this->assertDatabaseHas('products', [
        'id' => $productA->id,
        'stock' => 4,
    ]);
    $this->assertDatabaseHas('products', [
        'id' => $productB->id,
        'stock' => 5,
    ]);

});

test('can not confirm if order is already paid', function () {

    $user = User::factory()->create();
    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create([
        'user_id' => $user->id,
    ]);
    $order = Order::placeFor($user, $cart);

    $order->paid_at = now();
    $order->save();

    $id = 'tx12345';
    $clientTransactionId = 'cltx12345';

    $paymentMeta = [
        'transactionId' => $id,
        'clientTransactionId' => $clientTransactionId,
        'storeName' => 'ToEcommerce',
        'email' => 'customer@example.com',
        'gateway' => 'Payphone',
        'lastDigits' => '1234',
    ];

    $order->payphone_metadata = json_encode($paymentMeta);
    $order->save();

    PayphonePaymentGateway::shouldReceive('confirm')->andReturn(json_encode($paymentMeta));

    $this->withCookie('cart', $cart->ui_cart_id)->get(route('payments.confirm', [
        'id' => 'tx12345',
        'clientTransactionId' => 'cltx12345',
    ]))->assertRedirect(route('storefront.products'));

});

test('can not confirm if payphone transaction response has errors', function () {
    $user = User::factory()->create();
    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create([
        'user_id' => $user->id,
    ]);
    $order = Order::placeFor($user, $cart);

    $order->paid_at = now();
    $order->save();

    $id = 'tx12345';
    $clientTransactionId = 'cltx12345';

    $paymentMeta = [
        'transactionId' => $id,
        'clientTransactionId' => $clientTransactionId,
        'errorCode' => 1,
    ];

    PayphonePaymentGateway::shouldReceive('confirm')->andReturn(json_encode($paymentMeta));

    $this->withCookie('cart', $cart->ui_cart_id)->get(route('payments.confirm', [
        'id' => 'tx12345',
        'clientTransactionId' => 'cltx12345',
    ]))->assertRedirect(route('storefront.products'));
});

it('sends a confirmation email with link to order page', function () {

    Mail::fake();

    confirmation();

    $order = $this->order;
    $orderConfirmedMailable = new OrderConfirmedMailable($order);

    Mail::assertSent(function (OrderConfirmedMailable $mail) use ($order) {
        return $order->id === $mail->order->id
            && $order->fresh()->code === $mail->order->code
            && $mail->hasTo($order->user->email)
            && $mail->hasSubject('Orden Confirmada')
            && $mail->hasFrom(env('MAIL_FROM_ADDRESS'));
    });

    $orderConfirmedMailable->assertSeeInHtml('Orden Confirmada');

    // todo: test link in email
    /* $orderConfirmedMailable->assertSeeInHtml(route('filament.customer.resources.orders.view', [
        'record' => 'CONFIRMATIONCODE123456789',
    ])); */

});
