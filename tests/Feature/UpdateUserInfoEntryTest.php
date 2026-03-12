<?php

use App\Models\User;
use App\Models\UserInfoEntry;

test('customer can update use info entry', function () {

    $this->withoutExceptionHandling();

    $user = User::factory()->create();

    $userInfoEntry = UserInfoEntry::factory()->create([
        'user_id' => $user->id,
        'type' => 'billing',
        'is_main' => true,
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    $response = $this->actingAs($user)->put(route('storefront.user-info-entry.update', [
        'id' => $userInfoEntry->id,
    ]), [
        'first_name' => 'Jane',
        'last_name' => 'Soulivan',
        'type' => 'billing',
        'email' => 'jane.soulivan@example.com',
        'is_main' => true,
        'address' => $userInfoEntry->address,
        'city' => $userInfoEntry->city,
        'state' => $userInfoEntry->state,
        'country' => $userInfoEntry->country,
        'zipcode' => $userInfoEntry->zipcode,
        'phone' => $userInfoEntry->phone,

    ]);

    $response->assertStatus(302);

    $this->assertDatabaseHas('user_info_entries', [
        'id' => $userInfoEntry->id,
        'first_name' => 'Jane',
        'last_name' => 'Soulivan',
        'email' => 'jane.soulivan@example.com',
    ]);

});
