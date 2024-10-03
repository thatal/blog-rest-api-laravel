<?php

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
    Category::factory()->create(['name' => 'Tech']);
    Post::factory()->count(10)->create();
});

it('can search posts by title', function () {
    $post = Post::factory()->create(['title' => 'Unique Post Title']);

    $response = $this->getJson('/api/posts?search=Unique');

    $response->assertStatus(200)
             ->assertJsonFragment(['title' => 'Unique Post Title']);
});

it('can filter posts by category', function () {
    $category = Category::factory()->create(['name' => 'Lifestyle']);
    $post = Post::factory()->create();
    $post->categories()->attach($category->id);

    $response = $this->getJson('/api/posts?category=Lifestyle');

    $response->assertStatus(200)
             ->assertJsonFragment(['name' => 'Lifestyle']);
});

it('can paginate posts', function () {
    $response = $this->getJson('/api/posts?page=1');

    $response->assertStatus(200)
             ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'title', 'content']
                ],
                'links',
                'meta'
             ]);
});

