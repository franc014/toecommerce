<?php

use App\Models\User;

it('gives successful response for home page', function () {
    $response = $this->get(route('storefront.home'));
    $response->assertStatus(200);
});

it('gives successful response for products page', function () {
    $response = $this->get(route('storefront.products'));
    $response->assertStatus(200);
});

it('gives successful response for checkout page', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('storefront.checkout'));
    $response->assertStatus(200);
});
