<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(2);
        $slug = Str::slug($title);

        return [
            'title' => $title,
            'description' => fake()->text(),
            'slug' => $slug,
            'main_image' => fake()->imageUrl(),
            'video' => fake()->url(),
            'status' => fake()->randomElement(ProductStatus::cases()),
            'price' => 100.25,
            'stock' => fake()->numberBetween(0, 200),
            'sku' => fake()->ean13(),
            'published_at' => null,
            'stock_threshold_for_customers' => 10,

        ];
    }

    public function draft(): Factory
    {
        return $this->state([
            'status' => ProductStatus::DRAFT,
            'published_at' => null,
        ]);
    }

    public function published(): Factory
    {
        return $this->state([
            'status' => ProductStatus::ACTIVE,
            'published_at' => now(),
        ]);
    }

    public function archived(): Factory
    {
        return $this->state([
            'status' => ProductStatus::ARCHIVED,
            'archived_at' => now(),
        ]);
    }
}
