<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $title = $this->faker->sentence(3),
            'slug' => Str::slug($title) . '-' . rand(100, 999),
            'description' => $this->faker->paragraph(),
            'location' => $this->faker->city(),
            'price' => $this->faker->numberBetween(100000, 2500000),
            'tipo' => $this->faker->randomElement(['piso', 'casa', 'villa']),
            'bedrooms' => rand(1, 6),
            'bathrooms' => rand(1, 4),
            'area' => rand(50, 500),
            'is_featured' => $this->faker->boolean(60),
            'status' => 'published',
            'thumbnail' => null,
            'image' => 'https://placehold.co/600x400?text=Propiedad',
        ];
    }
}
