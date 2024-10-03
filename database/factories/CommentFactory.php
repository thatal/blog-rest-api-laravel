<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition()
    {
        return [
            'content' => $this->faker->paragraph(), // Generate random content for the comment
            'post_id' => Post::factory(), // Create a related post
            'user_id' => User::factory(), // Create a related user
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
