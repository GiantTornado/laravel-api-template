<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(nb: 3, asText: true);

        return [
            'title' => $title,
            'slug' => str()->slug($title, '-'),
            'description' => fake()->realText(maxNbChars: 200),
            'published_at' => fake()->dateTimeBetween('-1 years', '+1 months'),
        ];
    }
}
