<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'cart_id' => Cart::factory(),
            'code' => fake()->uuid(),
            'total_amount' => fake()->randomFloat(2, 50, 300),
            'total_with_taxes' => fake()->randomFloat(2, 50, 300),
            'total_without_taxes' => fake()->randomFloat(2, 50, 300),
            'total_computed_taxes' => fake()->randomFloat(2, 50, 300),
        ];
    }

    public function paid(): Factory
    {
        return $this->state([
            'paid_at' => now(),
        ]);
    }
}
