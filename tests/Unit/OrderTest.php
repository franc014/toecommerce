<?php

use App\Exceptions\CartAlreadyPaidException;
use App\Exceptions\PlaceOrderForEmptyCartException;
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
    $cart = Cart::factory()->create([
        'user_id' => $user->id,
    ]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'price' => 100,
        'quantity' => 2,
        'taxes' => json_encode([
            ['name' => 'IVA',
                'percentage' => 15],
            ['name' => 'ISD',
                'percentage' => 10],
        ]),
        'total' => 200,
        'total_with_taxes' => 2 * 100 * (1 + 0.15 + 0.10), // 250
        'computed_taxes' => 2 * 100 * (0.15 + 0.10), // 50
    ]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'price' => 30,
        'quantity' => 3,
        'taxes' => json_encode([
            ['name' => 'IVA',
                'percentage' => 15],
        ]),
        'total' => 90,
        'total_with_taxes' => 3 * 30 * (1 + 0.15),
        'computed_taxes' => 3 * 30 * (0.15),
    ]);

    $order = Order::placeFor($user, $cart->fresh());

    expect($order)->toBeInstanceOf(Order::class);
    expect($order->cart_id)->toBe($cart->id);
    expect($order->user_id)->toBe($user->id);
    expect($order->code)->toBe('01HRDBNHHCKNW2AK4Z29SN82T9');
    expect($order->paid_at)->toBeNull();

    expect($order->total_amount)->toBe($cart->fresh()->total_amount);
    expect($order->total_with_taxes)->toBe($cart->fresh()->total_with_taxes);
    expect($order->total_without_taxes)->toBe($cart->fresh()->total_without_taxes);
    expect($order->total_computed_taxes)->toBe($cart->fresh()->total_computed_taxes);
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
    expect($order->orderItems[0]->cart_item_id)->toBe($cart->items[0]->id);
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
    expect($order->orderItems[1]->cart_item_id)->toBe($cart->items[1]->id);
    expect($order->orderItems[1]->title)->toBe($cart->items[1]->title);
    expect($order->orderItems[1]->slug)->toBe($cart->items[1]->slug);
    expect($order->orderItems[1]->taxes)->toBe($cart->items[1]->taxes);
    expect($order->orderItems[1]->total)->toBe($cart->items[1]->total);
    expect($order->orderItems[1]->total_with_taxes)->toBe($cart->items[1]->total_with_taxes);
    expect($order->orderItems[1]->computed_taxes)->toBe($cart->items[1]->computed_taxes);
    expect($order->orderItems[1]->price)->toBe($cart->items[1]->price);
    expect($order->orderItems[1]->quantity)->toBe($cart->items[1]->quantity);

});

test('can add a new order item', function () {

    $user = User::factory()->create();
    $cart = Cart::factory()->has(CartItem::factory()->count(2), 'items')->create([
        'user_id' => $user->id,
    ]);

    $order = Order::placeFor($user, $cart->fresh());

    $newItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
    ]);

    expect($order->orderItems)->toHaveCount(3);

    expect($order->orderItems[2]->order_id)->toBe($order->id);
    expect($order->orderItems[2]->purchasable_id)->toBe($newItem->purchasable_id);
    expect($order->orderItems[2]->purchasable_type)->toBe($newItem->purchasable_type);
    expect($order->orderItems[2]->cart_item_id)->toBe($newItem->id);
    expect($order->orderItems[2]->title)->toBe($newItem->title);
    expect($order->orderItems[2]->slug)->toBe($newItem->slug);
    expect($order->orderItems[2]->taxes)->toBe($newItem->taxes);
    expect($order->orderItems[2]->total)->toBe($newItem->total);
    expect($order->orderItems[2]->total_with_taxes)->toBe($newItem->total_with_taxes);
    expect($order->orderItems[2]->computed_taxes)->toBe($newItem->computed_taxes);
    expect($order->orderItems[2]->price)->toBe($newItem->price);
    expect($order->orderItems[2]->quantity)->toBe($newItem->quantity);

    expect($order->fresh()->total_amount)->toBe($cart->fresh()->total_amount);
    expect($order->fresh()->total_with_taxes)->toBe($cart->fresh()->total_with_taxes);
    expect($order->fresh()->total_without_taxes)->toBe($cart->fresh()->total_without_taxes);
    expect($order->fresh()->total_computed_taxes)->toBe($cart->fresh()->total_computed_taxes);
    expect($order->fresh()->paid_at)->toBeNull();

});

test('can update an order item', function () {

    $user = User::factory()->create();
    $cart = Cart::factory()->create([
        'user_id' => $user->id,
    ]);

    $item = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'price' => 100,
        'quantity' => 2,
        'taxes' => json_encode(
            [
                [
                    'name' => 'IVA',
                    'percentage' => 15,
                ],
            ],
        ),
        'total' => 200,
        'total_with_taxes' => 200 * (1 + 0.15),
        'computed_taxes' => 200 * 0.15,

    ]);

    $order = Order::placeFor($user, $cart->fresh());

    $newQuantity = 3;

    $item->quantity = $newQuantity;
    $item->total = $newQuantity * $item->price;
    $item->total_with_taxes = $newQuantity * $item->price * (1 + 0.15);
    $item->computed_taxes = $item->total_with_taxes - $item->total;
    $item->save();

    expect($order->orderItems)->toHaveCount(1);

    expect($order->orderItems[0]->order_id)->toBe($order->id);
    expect($order->orderItems[0]->purchasable_id)->toBe($item->purchasable_id);
    expect($order->orderItems[0]->purchasable_type)->toBe($item->purchasable_type);
    expect($order->orderItems[0]->cart_item_id)->toBe($item->id);
    expect($order->orderItems[0]->title)->toBe($item->title);
    expect($order->orderItems[0]->slug)->toBe($item->slug);
    expect($order->orderItems[0]->taxes)->toBe($item->taxes);
    expect($order->orderItems[0]->total)->toBe($item->total);
    expect($order->orderItems[0]->total_with_taxes)->toBe($item->total_with_taxes);
    expect($order->orderItems[0]->computed_taxes)->toBe($item->computed_taxes);
    expect($order->orderItems[0]->price)->toBe($item->price);
    expect($order->orderItems[0]->quantity)->toBe($newQuantity);

    expect($order->fresh()->total_amount)->toBe($item->total_with_taxes);
    expect($order->fresh()->total_with_taxes)->toBe($item->total);
    expect($order->fresh()->total_without_taxes)->toBe(0.0);
    expect($order->fresh()->paid_at)->toBeNull();

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
