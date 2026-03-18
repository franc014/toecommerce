<?php

namespace Database\Factories;

use App\Models\UserInfoEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserInfoEntry>
 */
class UserInfoEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['billing', 'shipping']),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->email,
            'country' => 'USA',
            'state' => $this->faker->state,
            'city' => $this->faker->city,
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'zipcode' => '12345',
        ];
    }
}
