<?php

use App\Models\User;
use App\Models\UserInfoEntry;

test('can have invoice information entries', function () {
    $user = User::factory()->create();
    $billingInfoEntry = UserInfoEntry::factory()->create([
        'user_id' => $user->id,
        'type' => 'billing',
    ]);

    $shippingInfoEntry = UserInfoEntry::factory()->create([
        'user_id' => $user->id,
        'type' => 'shipping',
    ]);

    expect($user->billingInfoEntry->first()->id)->toBe($billingInfoEntry->id);
    expect($user->shippingInfoEntry->first()->id)->toBe($shippingInfoEntry->id);

});

test('can have a main billing info entry', function () {
    $user = User::factory()->create();
    UserInfoEntry::factory()->create([
        'user_id' => $user->id,
        'type' => 'billing',
        'is_main' => true
    ]);

    UserInfoEntry::factory()->create([
         'user_id' => $user->id,
         'type' => 'billing',
         'is_main' => false
     ]);

    expect($user->billingInfoEntry[0]->is_main)->toBe(1);
    expect($user->billingInfoEntry[1]->is_main)->toBe(0);

    expect($user->mainBillingInfoEntry()->id)->toBe($user->billingInfoEntry[0]->id);
    expect($user->mainBillingInfoEntry()->id)->not()->toBe($user->billingInfoEntry[1]->id);
});

test('can have a main shipping info entry', function () {
    $user = User::factory()->create();
    UserInfoEntry::factory()->create([
        'user_id' => $user->id,
        'type' => 'shipping',
        'is_main' => true
    ]);

    UserInfoEntry::factory()->create([
         'user_id' => $user->id,
         'type' => 'shipping',
         'is_main' => false
     ]);

    expect($user->shippingInfoEntry[0]->is_main)->toBe(1);
    expect($user->shippingInfoEntry[1]->is_main)->toBe(0);

    expect($user->mainShippingInfoEntry()->id)->toBe($user->shippingInfoEntry[0]->id);
    expect($user->mainShippingInfoEntry()->id)->not()->toBe($user->shippingInfoEntry[1]->id);
});

test('checking user has registered billing info', function () {
    $user = User::factory()->create();
    expect($user->has_billing_info)->toBeFalse();

    UserInfoEntry::factory()->create([
        'user_id' => $user->id,
        'type' => 'billing',
    ]);

    expect($user->fresh()->has_billing_info)->toBeTrue();
});

test('checking user has registered shipping info', function () {
    $user = User::factory()->create();
    expect($user->has_shipping_info)->toBeFalse();

    UserInfoEntry::factory()->create([
        'user_id' => $user->id,
        'type' => 'shipping',
    ]);

    expect($user->fresh()->has_shipping_info)->toBeTrue();
});
