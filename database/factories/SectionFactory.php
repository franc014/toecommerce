<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Section>
 */
class SectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $content = [
              'type' => 'heading',
              'data' => [
                  'content' => fake()->sentence(4),
                  'level' => 'h1',
              ],
          ];

        return [
            'title' => fake()->sentence(4),
            'description' => fake()->sentence(4),
            'slug' => fake()->slug(),
            'content' => $content,
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }
}
