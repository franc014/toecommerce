<?php

namespace Database\Factories;

use App\Enums\PageStatus;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Page::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $title = fake()->sentence();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->text(),
            'status' => fake()->randomElement(['draft', 'published']),
            'published_at' => fake()->dateTime(),
            'metatags' => [],
            'route' => Str::slug($title),
        ];
    }

    public function published(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => PageStatus::PUBLISHED,
                'published_at' => now(),
            ];
        });
    }
}
