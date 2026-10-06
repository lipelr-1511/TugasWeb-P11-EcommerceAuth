<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'user_id'     => User::factory(),
            'title'       => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'price'       => fake()->numberBetween(10, 5000) * 1000,
            'stock'       => fake()->numberBetween(0, 100),
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => ['stock' => 0]);
    }
}
