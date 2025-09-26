<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CartItem>
 */
class CartItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomFloat(2, 50, 300);
        $quantity = $this->faker->randomNumber(1, 5);
        $total = $price * $quantity;

        return [
            'cart_id' => Cart::factory(),
            'purchasable_id' => Product::factory(),
            'title' => $this->faker->sentence,
            'slug' => $this->faker->slug,
            'price' => $price,
            'quantity' => $quantity,
            'total' => $total,
            'total_with_taxes' => $total,
            'purchasable_type' => Product::class,
        ];
    }
}
