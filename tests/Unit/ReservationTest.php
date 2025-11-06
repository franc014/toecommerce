<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;

test('cart items can be reserved', function () {

    $user = User::factory()->create();

    $productA = Product::factory()->create([
        'title' => 'Product A',
        'slug' => 'product-a',
    ]);

    $productB = Product::factory()->create([
        'title' => 'Product B',
        'slug' => 'product-b',
    ]);

    $cart = Cart::factory()->create([
        'user_id' => $user->id,
    ]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'purchasable_id' => $productA->id,
        'title' => $productA->title,
        'slug' => $productA->slug,
        'purchasable_type' => Product::class,
        'quantity' => 2,
    ]);

    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'purchasable_id' => $productB->id,
        'purchasable_type' => Product::class,
        'title' => $productB->title,
        'slug' => $productB->slug,
        'quantity' => 3,
    ]);


    $cart->reserveItemsFor($user);

    expect($user->reservations)->toHaveCount(2);


    expect($user->reservations[0]->purchasable_id)->toBe($cart->items[0]->purchasable_id);
    expect($user->reservations[1]->purchasable_id)->toBe($cart->items[1]->purchasable_id);

    expect($user->reservations[0]->purchasable_type)->toBe($cart->items[0]->purchasable_type);
    expect($user->reservations[1]->purchasable_type)->toBe($cart->items[1]->purchasable_type);

    expect($user->reservations[0]->purchasable->title)->toBe($cart->items[0]->title);
    expect($user->reservations[1]->purchasable->title)->toBe($cart->items[1]->title);

    expect($user->reservations[0]->purchasable->slug)->toBe($cart->items[0]->slug);
    expect($user->reservations[1]->purchasable->slug)->toBe($cart->items[1]->slug);

    expect($user->reservations[0]->cart_id)->toBe($cart->id);
    expect($user->reservations[1]->cart_id)->toBe($cart->id);

    expect($user->reservations[0]->user_id)->toBe($user->id);
    expect($user->reservations[1]->user_id)->toBe($user->id);

    expect($user->reservations[0]->allowed_quantity)->toBe($cart->items[0]->quantity);
    expect($user->reservations[0]->unavailable_quantity)->toBe(0);

    expect($user->reservations[1]->allowed_quantity)->toBe($cart->items[1]->quantity);
    expect($user->reservations[1]->unavailable_quantity)->toBe(0);


});
