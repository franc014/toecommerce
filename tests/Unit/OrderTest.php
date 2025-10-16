<?php

use App\Exceptions\CartAlreadyPaidException;
use App\Exceptions\PlaceOrderForEmptyCartException;
use App\Facades\PayphoneClientTransactionIdGenerator;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Str;
use Symfony\Component\Uid\Ulid;

it('belongs to a user', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($order->user->id)->toBe($user->id);
});

test('can create an order', function () {

    Str::createUlidsUsing(function () {
        return new Ulid('01HRDBNHHCKNW2AK4Z29SN82T9');
    });
    $user = User::factory()->create();
    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create();

    $order = Order::placeFor($user, $cart);


    expect($order)->toBeInstanceOf(Order::class);
    expect($order->cart_id)->toBe($cart->id);
    expect($order->user_id)->toBe($user->id);
    expect($order->code)->toBe('01HRDBNHHCKNW2AK4Z29SN82T9');
    expect($order->paid_at)->toBeNull();
    expect($order->total_amount * 100)->toBe($cart->total_amount);
    //expect($order->total_with_taxes)->toBe($cart->total_with_taxes / 100);
    expect($order->total_without_taxes)->toBe($cart->total_without_taxes / 100);
    expect($order->total_computed_taxes)->toBe($cart->total_computed_taxes / 100);

    expect($order->paid_at)->toBeNull();
});

test('can create order items', function () {
    $user = User::factory()->create();
    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create();

    $order = Order::placeFor($user, $cart);

    expect($order->orderItems)->toHaveCount(2);
    expect($order->orderItems[0]->order_id)->toBe($order->id);
    expect($order->orderItems[0]->purchasable_id)->toBe($cart->items[0]->purchasable_id);
    expect($order->orderItems[0]->purchasable_type)->toBe($cart->items[0]->purchasable_type);
    expect($order->orderItems[0]->title)->toBe($cart->items[0]->title);
    expect($order->orderItems[0]->slug)->toBe($cart->items[0]->slug);
    expect($order->orderItems[0]->taxes)->toBe($cart->items[0]->taxes);
    expect($order->orderItems[0]->total)->toBe($cart->items[0]->total);
    expect($order->orderItems[0]->total_with_taxes)->toBe($cart->items[0]->total_with_taxes);
    expect($order->orderItems[0]->computed_taxes)->toBe($cart->items[0]->computed_taxes);
    expect($order->orderItems[0]->price)->toBe($cart->items[0]->price);
    expect($order->orderItems[0]->quantity)->toBe($cart->items[0]->quantity);

    expect($order->orderItems[1]->order_id)->toBe($order->id);
    expect($order->orderItems[1]->purchasable_id)->toBe($cart->items[1]->purchasable_id);
    expect($order->orderItems[1]->purchasable_type)->toBe($cart->items[1]->purchasable_type);
    expect($order->orderItems[1]->title)->toBe($cart->items[1]->title);
    expect($order->orderItems[1]->slug)->toBe($cart->items[1]->slug);
    expect($order->orderItems[1]->taxes)->toBe($cart->items[1]->taxes);
    expect($order->orderItems[1]->total)->toBe($cart->items[1]->total);
    expect($order->orderItems[1]->total_with_taxes)->toBe($cart->items[1]->total_with_taxes);
    expect($order->orderItems[1]->computed_taxes)->toBe($cart->items[1]->computed_taxes);
    expect($order->orderItems[1]->price)->toBe($cart->items[1]->price);
    expect($order->orderItems[1]->quantity)->toBe($cart->items[1]->quantity);

});

test('trying to place an order for a cart with no items throws an exception', function () {

    Exceptions::fake();
    $cart = Cart::factory()->create();
    $user = User::factory()->create();

    $this->assertThrows(
        fn () => Order::placeFor($user, $cart),
        PlaceOrderForEmptyCartException::class,
    );
});

test('trying to place an order for a cart already paid throws an exception', function () {

    Exceptions::fake();
    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create([
        'paid_at' => now()->subDay(),
    ]);
    $user = User::factory()->create();

    $this->assertThrows(
        fn () => Order::placeFor($user, $cart),
        CartAlreadyPaidException::class,
    );
});

it('is confirmed', function () {

    $order = Order::factory()->create([
        'paid_at' => now()->subDay(),
    ]);

    expect($order->isConfirmed())->toBeTrue();
});
