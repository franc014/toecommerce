<?php

use App\Models\User;
use App\Models\UserInfoEntry;
use Illuminate\Testing\Fluent\AssertableJson;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'customer@example.com',
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

test('can show the customer information for invoice and shipping', function () {
    $invoiceInfo = UserInfoEntry::factory()->create([
        'user_id' => $this->user->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'type' => 'billing',
        'country' => 'United States',
        'state' => 'New York',
        'city' => 'New York',
        'address' => '123 Main St',
        'phone' => '1234567890',
        'zipcode' => '12345',
        'email' => 'customer@example.com',
    ]);

    $shippingInfo = UserInfoEntry::factory()->create([
        'user_id' => $this->user->id,
        'first_name' => 'Jane',
        'last_name' => 'Simmons',
        'type' => 'shipping',
        'country' => 'Canada',
        'state' => 'Ontario',
        'city' => 'Toronto',
        'address' => '456 Elm St',
        'phone' => '9876543210',
        'zipcode' => 'A1B2C3',
        'email' => 'customershipping@example.com',
    ]);

    $this->actingAs($this->user)
    ->get(route('storefront.checkout'))
    ->assertStatus(200)
    ->assertSee($invoiceInfo->first_name)
    ->assertSee($invoiceInfo->last_name)
    ->assertSee($invoiceInfo->email)
    ->assertSee($invoiceInfo->phone)
    ->assertSee($invoiceInfo->country)
    ->assertSee($invoiceInfo->state)
    ->assertSee($invoiceInfo->city)
    ->assertSee($invoiceInfo->address)
    ->assertSee($shippingInfo->first_name)
    ->assertSee($shippingInfo->last_name)
    ->assertSee($shippingInfo->email)
    ->assertSee($shippingInfo->phone)
    ->assertSee($shippingInfo->country)
    ->assertSee($shippingInfo->state)
    ->assertSee($shippingInfo->city)
    ->assertSee($shippingInfo->address);

});


test('shows billing information form if user has no billing info', function () {
    $response = $this->actingAs($this->user)
    ->get(route('storefront.checkout'));

    expect($response->inertiaProps('auth.user.has_billing_info'))->toBeFalse();

});

test('shows shipping information form if user has no shipping info', function () {
    $response = $this->actingAs($this->user)
    ->get(route('storefront.checkout'));

    expect($response->inertiaProps('auth.user.has_shipping_info'))->toBeFalse();
});

test('customer can send billing info', function () {
    $this->actingAs($this->user)
    ->post(route('user-info-entry.store'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'type' => 'billing',
        'country' => 'United States',
        'state' => 'New York',
        'city' => 'New York',
        'address' => '123 Main St',
        'phone' => '1234567890',
        'zipcode' => '12345',
        'email' => 'customer@example.com',
    ])
    ->assertStatus(200)
    ->assertJson(fn (AssertableJson $json) => $json->where('message', 'User info entry created successfully'));

    $this->assertDatabaseHas('user_info_entries', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'type' => 'billing',
        'country' => 'United States',
        'state' => 'New York',
        'city' => 'New York',
        'address' => '123 Main St',
        'phone' => '1234567890',
        'zipcode' => '12345',
        'email' => 'customer@example.com',
    ]);
});
