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
});

it('can retrieve all posts', function () {
    Post::factory(10)->create();

    $response = $this->getJson('/api/posts');

    // dd($response);

    $response->assertStatus(200)
             ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'title', 'content', 'user_id', 'created_at', 'updated_at'],
                ],
                'links',
                'meta'
             ]);
});

it('can create a post', function () {
    $category = Category::factory()->create();
    $data = [
        'title' => 'New Post Title',
        'content' => 'Post content',
        'category_ids' => [$category->id],
    ];

    $response = $this->postJson('/api/posts', $data);

    $response->assertStatus(201)
             ->assertJsonFragment(['title' => 'New Post Title']);
});

it('can show a post', function () {
    $post = Post::factory()->create();

    $response = $this->getJson("/api/posts/{$post->id}");

    $response->assertStatus(200)
             ->assertJsonFragment(['id' => $post->id, 'title' => $post->title]);
});

it('can update a post', function () {
    $post = Post::factory()->create();
    $data = ['title' => 'Updated Post Title', 'content' => 'Updated content'];

    $response = $this->putJson("/api/posts/{$post->id}", $data);

    $response->assertStatus(200)
             ->assertJsonFragment(['title' => 'Updated Post Title']);
});

it('can delete a post', function () {
    $post = Post::factory()->create();

    $response = $this->deleteJson("/api/posts/{$post->id}");
    $response->assertStatus(200)
             ->assertJson(['success' => true, 'message' => 'Post deleted successfully.']);
});

