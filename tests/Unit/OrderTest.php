<?php

use App\Exceptions\CartAlreadyPaidException;
use App\Exceptions\PlaceOrderForEmptyCartException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Tax;
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

    $taxA = Tax::factory()->create(['percentage' => 15, 'description' => 'IVA 15%', 'name' => 'IVA']);
    $taxB = Tax::factory()->create(['percentage' => 10, 'description' => 'ISD 10%', 'name' => 'ISD']);

    $productA = Product::factory()->create([
       'title' => 'Product A',
       'slug' => 'product-a',
       'price' => 100,

    ]);
    $productB = Product::factory()->create([
        'title' => 'Product B',
        'slug' => 'product-b',
        'price' => 30,

    ]);

    $productA->taxes()->attach([$taxA->id, $taxB->id]);
    $productB->taxes()->attach([$taxA->id, $taxB->id]);

    $user = User::factory()->create();
    $cart = Cart::factory()->create([
        'user_id' => $user->id,
    ]);

    $ciA = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'purchasable_id' => $productA->id,
        'purchasable_type' => Product::class,
        'price' => 100,
        'quantity' => 2,
        'taxes' => $productA->taxesToJson(),
        'total' => 200,
        'total_with_taxes' => 2 * 100 * (1 + 0.15 + 0.10), // 250
        'computed_taxes' => 2 * 100 * (0.15 + 0.10), // 50
    ]);

    $ciB = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'purchasable_id' => $productB->id,
        'purchasable_type' => Product::class,
        'price' => 30,
        'quantity' => 3,
        'taxes' => $productB->taxesToJson(),
        'total' => 90,
        'total_with_taxes' => 3 * 30 * (1 + 0.15 + 0.10),
        'computed_taxes' => 3 * 30 * (0.15 + 0.10),
    ]);


    $productA->reserve($user, $cart, $ciA->quantity);
    $productB->reserve($user, $cart, $ciB->quantity);

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
    $productA = Product::factory()->create([
        'title' => 'Product A',
        'slug' => 'product-a',
        'price' => 100,

    ]);
    $productB = Product::factory()->create([
        'title' => 'Product B',
        'slug' => 'product-b',
        'price' => 30,

    ]);

    $tax = Tax::factory()->create([
        'name' => 'IVA',
        'percentage' => 15
    ]);

    $productA->taxes()->attach($tax->id);
    $productB->taxes()->attach($tax->id);

    $user = User::factory()->create();
    $cart = Cart::factory()->create([
        'user_id' => $user->id,
    ]);
    $ciA = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'purchasable_id' => $productA->id,
        'purchasable_type' => Product::class,
        'title' => $productA->title,
        'slug' => $productA->slug,
        'price' => 100,
        'quantity' => 2,
        'total' => 200,

    ]);

    $ciB = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'purchasable_id' => $productB->id,
        'purchasable_type' => Product::class,
        'title' => $productB->title,
        'slug' => $productB->slug,
        'price' => 30,
        'quantity' => 3,
        'total' => 90,


    ]);

    $reservationA = $productA->reserve($user, $cart, $ciA->quantity);
    $reservationB = $productB->reserve($user, $cart, $ciB->quantity);

    expect($ciA->purchasable->id)->toBe($reservationA->purchasable->id);
    expect($ciA->purchasable->title)->toBe($reservationA->purchasable->title);

    expect($ciB->purchasable->id)->toBe($reservationB->purchasable->id);
    expect($ciB->purchasable->title)->toBe($reservationB->purchasable->title);


    $order = Order::placeFor($user, $cart);

    expect($order->orderItems)->toHaveCount(2);

    expect($order->orderItems[0]->order_id)->toBe($order->id);
    expect($order->orderItems[0]->purchasable_id)->toBe($productA->id);
    expect($order->orderItems[0]->purchasable_type)->toBe(Product::class);
    expect($order->orderItems[0]->cart_item_id)->toBe($cart->items[0]->id);
    expect($order->orderItems[0]->title)->toBe($productA->title);
    expect($order->orderItems[0]->slug)->toBe($productA->slug);
    expect($order->orderItems[0]->taxes)->toBe($productA->taxesToJson());
    expect($order->orderItems[0]->price)->toBe($productA->price);
    expect($order->orderItems[0]->cart_quantity)->toBe($cart->items[0]->quantity);
    expect($order->orderItems[0]->allowed_quantity)->toBe($cart->items[0]->quantity);
    expect($order->orderItems[0]->unavailable_quantity)->toBe(0);
    expect($order->orderItems[0]->total)->toBe($productA->price * $cart->items[0]->quantity);
    expect($order->orderItems[0]->total_with_taxes)->toBe($productA->priceWithTaxes() * $cart->items[0]->quantity);
    expect($order->orderItems[0]->computed_taxes)->toBe($productA->computedTaxes() * $cart->items[0]->quantity);

    expect($order->orderItems[1]->order_id)->toBe($order->id);
    expect($order->orderItems[1]->purchasable_id)->toBe($productB->id);
    expect($order->orderItems[1]->purchasable_type)->toBe(Product::class);
    expect($order->orderItems[1]->cart_item_id)->toBe($cart->items[1]->id);
    expect($order->orderItems[1]->title)->toBe($productB->title);
    expect($order->orderItems[1]->slug)->toBe($productB->slug);
    expect($order->orderItems[1]->taxes)->toBe($productB->taxesToJson());
    expect($order->orderItems[1]->price)->toBe($productB->price);
    expect($order->orderItems[1]->cart_quantity)->toBe($cart->items[1]->quantity);
    expect($order->orderItems[1]->allowed_quantity)->toBe($cart->items[1]->quantity);
    expect($order->orderItems[1]->unavailable_quantity)->toBe(0);
    expect($order->orderItems[1]->total)->toBe($productB->price * $cart->items[1]->quantity);
    expect($order->orderItems[1]->total_with_taxes)->toBe($productB->priceWithTaxes() * $cart->items[1]->quantity);
    expect($order->orderItems[1]->computed_taxes)->toBe($productB->computedTaxes() * $cart->items[1]->quantity);



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
