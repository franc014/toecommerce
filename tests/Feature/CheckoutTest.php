<?php

use App\Models\User;
use App\Models\UserInfoEntry;

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
    ->post(route('storefront.user-info-entry.store'), [
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
    ->assertStatus(302)
    ->assertRedirect(route('storefront.checkout'));

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

test('customer can send shipping info', function () {
    $this->actingAs($this->user)
    ->post(route('storefront.user-info-entry.store'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'type' => 'shipping',
        'country' => 'United States',
        'state' => 'New York',
        'city' => 'New York',
        'address' => '123 Main St',
        'phone' => '1234567890',
        'zipcode' => '12345',
        'email' => 'customer@example.com',
    ])
    ->assertStatus(302)
    ->assertRedirect(route('storefront.checkout'));

    $this->assertDatabaseHas('user_info_entries', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'type' => 'shipping',
        'country' => 'United States',
        'state' => 'New York',
        'city' => 'New York',
        'address' => '123 Main St',
        'phone' => '1234567890',
        'zipcode' => '12345',
        'email' => 'customer@example.com',
    ]);
});

test('guests can not send customer info', function () {
    $this->post(route('storefront.user-info-entry.store'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'type' => 'shipping',
        'country' => 'United States',
        'state' => 'New York',
        'city' => 'New York',
        'address' => '123 Main St',
        'phone' => '1234567890',
        'zipcode' => '12345',
        'email' => 'customer@example.com',
    ])
    ->assertStatus(302)
    ->assertRedirect(route('login'));

    $this->assertDatabaseMissing('user_info_entries', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'type' => 'shipping',
        'country' => 'United States',
        'state' => 'New York',
        'city' => 'New York',
        'address' => '123 Main St',
        'phone' => '1234567890',
        'zipcode' => '12345',
        'email' => 'customer@example.com',
    ]);
});




//validation user info


function sendUserInfo($data)
{
    $response = test()->actingAs(test()->user)
    ->post(route('storefront.user-info-entry.store'), $data);

    return $response;
}

function validParams(array $overrides = [])
{
    return [
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
        ...$overrides
    ];
}


test('first name is required', function () {
    sendUserInfo(validParams(['first_name' => '']))
    ->assertSessionHasErrors('first_name');
});

test('first name has at least 2 characters', function () {
    sendUserInfo(validParams(['first_name' => 'k']))
    ->assertSessionHasErrors('first_name');
});

test('first name has no more than 16 characters', function () {
    sendUserInfo(validParams(['first_name' => str_repeat('a', 17)]))
    ->assertSessionHasErrors('first_name');
});


test('last name is required', function () {
    sendUserInfo(validParams(['last_name' => '']))
    ->assertSessionHasErrors('last_name');
});

test('last name has at least 2 characters', function () {
    sendUserInfo(validParams(['last_name' => 'k']))
    ->assertSessionHasErrors('last_name');
});

test('last name has no more than 16 characters', function () {
    sendUserInfo(validParams(['last_name' => str_repeat('a', 17)]))
    ->assertSessionHasErrors('last_name');
});

test('country is not required', function () {
    sendUserInfo(validParams(['country' => '']))
    ->assertSessionHasNoErrors('country');
});


test('country has no more than 24 characters', function () {
    sendUserInfo(validParams(['country' => str_repeat('a', 25)]))
    ->assertSessionHasErrors('country');
});

test('state is not required', function () {
    sendUserInfo(validParams(['state' => '']))
    ->assertSessionHasNoErrors('state');
});

test('state has no more than 24 characters', function () {
    sendUserInfo(validParams(['state' => str_repeat('a', 25)]))
    ->assertSessionHasErrors('state');
});

test('city is required', function () {
    sendUserInfo(validParams(['city' => '']))
    ->assertSessionHasErrors('city');
});

test('city has at least 2 characters', function () {
    sendUserInfo(validParams(['city' => 'k']))
    ->assertSessionHasErrors('city');
});

test('city has no more than 24 characters', function () {
    sendUserInfo(validParams(['city' => str_repeat('a', 25)]))
    ->assertSessionHasErrors('city');
});

test('address is required', function () {
    sendUserInfo(validParams(['address' => '']))
    ->assertSessionHasErrors('address');
});

test('address has at least 2 characters', function () {
    sendUserInfo(validParams(['address' => 'k']))
    ->assertSessionHasErrors('address');
});

test('address has no more than 128 characters', function () {
    sendUserInfo(validParams(['address' => str_repeat('a', 129)]))
    ->assertSessionHasErrors('address');
});

test('phone is not required', function () {
    sendUserInfo(validParams(['phone' => '']))
    ->assertSessionHasNoErrors('phone');
});

test('phone has no more than 24 characters', function () {
    sendUserInfo(validParams(['phone' => str_repeat('a', 25)]))
    ->assertSessionHasErrors('phone');
});

test('zipcode is required', function () {
    sendUserInfo(validParams(['zipcode' => '']))
    ->assertSessionHasErrors('zipcode');
});

test('zipcode has at least 4 characters', function () {
    sendUserInfo(validParams(['zipcode' => 'fes']))
    ->assertSessionHasErrors('zipcode');
});

test('zipcode has no more than 6 characters', function () {
    sendUserInfo(validParams(['zipcode' => str_repeat('a', 7)]))
    ->assertSessionHasErrors('zipcode');
});

test('email is required', function () {
    sendUserInfo(validParams(['email' => '']))
    ->assertSessionHasErrors('email');
});

test('email should be valid', function () {
    sendUserInfo(validParams(['email' => 'invalid-email']))
    ->assertSessionHasErrors('email');
});

test('info type is required', function () {
    sendUserInfo(validParams(['type' => '']))
    ->assertSessionHasErrors('type');
});

test('info type can be billing or shipping', function () {
    sendUserInfo(validParams(['type' => 'another-type']))
    ->assertSessionHasErrors('type');
});

test('info type can be billing', function () {
    sendUserInfo(validParams(['type' => 'billing']))
    ->assertSessionHasNoErrors('type');
});

test('info type can be shipping', function () {
    sendUserInfo(validParams(['type' => 'shipping']))
    ->assertSessionHasNoErrors('type');
});
