<?php

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

it('can retrieve all posts', function () {
    Post::factory(10)->create()->each(function ($post) {
        if ($post->id % 2 === 0) {
            $post->addMedia(\Illuminate\Http\UploadedFile::fake()->image('test.jpg'))->toMediaCollection();
        }
        $post->categories()->attach(Category::factory()->create());
    });

    $response = $this->getJson('/api/posts');

    $response->assertStatus(200)
             ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id', 'title', 'content', 'created_at', 'updated_at', 'author' => ['id', 'name'], 'categories', 'comments_count', 'feature_image'
                    ],
                ],
                'links',
                'meta'
             ]);
});

it('can create a post', function () {
    $category = Category::factory()->create();
    
    // Case 1: Without feature image
    $dataWithoutImage = [
        'title' => 'New Post Title',
        'content' => 'Post content',
        'category_ids' => [$category->id],
    ];

    $responseWithoutImage = $this->postJson('/api/posts', $dataWithoutImage);

    $responseWithoutImage->assertStatus(201)
                         ->assertJsonFragment(['title' => 'New Post Title']);

    // Case 2: With feature image
    $dataWithImage = [
        'title' => 'New Post Title with Image',
        'content' => 'Post content with image',
        'category_ids' => [$category->id],
        'feature_image' => \Illuminate\Http\UploadedFile::fake()->image('test.jpg'),
    ];

    $responseWithImage = $this->postJson('/api/posts', $dataWithImage);

    $responseWithImage->assertStatus(201)
                      ->assertJsonFragment(['title' => 'New Post Title with Image']);

    $responseWithImage->assertJsonStructure(['feature_image']);
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

it('can create a post with feature image', function () {
    $category = Category::factory()->create();
    $data = [
        'title' => 'New Post Title',
        'content' => 'Post content',
        'category_ids' => [$category->id],
        'feature_image' => \Illuminate\Http\UploadedFile::fake()->image('test.jpg'),
    ];

    $response = $this->postJson('/api/posts', $data);

    $response->assertStatus(201)
             ->assertJsonFragment(['title' => 'New Post Title']);


    $this->assertFileExists(storage_path('app/public/' . $response->json('data.feature_image_url')));
});