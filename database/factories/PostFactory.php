<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(), // Generates a random title
            'content' => $this->faker->paragraph(), // Generates random content
            'user_id' => User::factory(), // Creates a user for the post
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
