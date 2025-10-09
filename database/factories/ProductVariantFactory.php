<?php

namespace Database\Factories;

use App\Enums\ProductSizes;
use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(1);
        $slug = str()->slug($title);


        $variantOptions = [
       [
           'name' => 'size',
           'values' => [
                ['value' => 'small'],
                ['value' => 'medium'],
                ['value' => 'large']
           ]
       ],

       [
           'name' => 'color',
           'values' => [
                ['value' => 'red'],
                ['value' => 'blue'],
                ['value' => 'green']
           ]
        ],

       [
           'name' => 'material',
           'values' => [
                ['value' => 'leather'],
                ['value' => 'fabric'],
                ['value' => 'synthetic']
           ]
        ]

    ];

        return [
            'title' => $title,
            'slug' => $slug.'-'.$this->faker->uuid(),
            'product_id' => Product::factory(),

            'price' => fake()->randomFloat(2, 50, 300),
            'status' => fake()->randomElement(ProductStatus::class),
            'sku' => fake()->uuid(),
            'stock' => fake()->numberBetween(100, 300),
            'variation' => json_encode($variantOptions),
        ];
    }

    public function published(): Factory
    {
        return $this->state([
            'status' => ProductStatus::ACTIVE,
            'published_at' => now(),
        ]);
    }

    public function draft(): Factory
    {
        return $this->state([
            'status' => ProductStatus::DRAFT,
            'published_at' => null,
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
