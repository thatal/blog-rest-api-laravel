<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word(), // Generates a unique category name
            'slug' => $this->faker->unique()->slug(), // Generates a unique slug for the category
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
