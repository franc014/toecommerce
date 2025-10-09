<?php

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'buyer@example.com',
        'phone' => '1234567890',
        'name' => 'John Doe',
    ]);
});

test('signed in user can access the checkout page', function () {
    $this->actingAs($this->user)
    ->get(route('storefront.checkout'))
    ->assertStatus(200);
});

test('guest users should login to access the checkout page', function () {
    $this->get(route('storefront.checkout'))
    ->assertRedirect(route('login'));
});
