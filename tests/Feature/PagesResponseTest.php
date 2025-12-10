<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;

it('gives successful response for home page', function () {
    $response = $this->get(route('storefront.home'));
    $response->assertStatus(200);
});

it('gives successful response for products page', function () {
    $response = $this->get(route('storefront.products'));
    $response->assertStatus(200);
});

it('gives successful response for collections page', function () {
    $response = $this->get(route('storefront.collections'));
    $response->assertStatus(200);
});

it('gives successful response for collection page', function () {
    $collection = Product::factory()->create();
    $response = $this->get(route('storefront.collection', ['collection' => $collection->slug]));
    $response->assertStatus(200);
});

it('gives successful response for product page', function () {
    $product = Product::factory()->published()->create();
    $response = $this->get(route('storefront.product', ['product' => $product->slug]));
    $response->assertStatus(200);
});

it('gives successful response for checkout page', function () {
    $cart = Cart::factory()->create();
    CartItem::factory()->create(['cart_id' => $cart->id]);
    $user = User::factory()->create();
    $response = $this->actingAs($user)->withCookie('cart', $cart->ui_cart_id)->get(route('storefront.checkout'));
    $response->assertStatus(200);
});
