<?php

use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a user and authenticate using Sanctum
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user, ['*']);
});

it('can retrieve all categories', function () {
    Category::factory(10)->create();

    $response = $this->getJson('/api/categories');

    $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'slug', 'description', 'posts_count', 'created_at', 'updated_at', 'category_image']
                ],
                'links',
                'meta'
            ]);
});

it('can create a category', function () {
    $data = ['name' => 'Tech', 'slug' => 'tech', 'description' => 'Technology Category'];

    $response = $this->postJson('/api/categories', $data);

    $response->assertStatus(201)
             ->assertJsonFragment(['name' => 'Tech', 'slug' => 'tech']);
});

it('can create a category with image', function () {
    $data = [
        'name' => 'Tech', 
        'slug' => 'tech',
        'description' => 'Technology Category', 
        'category_image' => UploadedFile::fake()->image('tech.jpg')
    ];

    $responseWithImage = $this->postJson('/api/categories', $data);

    $responseWithImage->assertStatus(201)
             ->assertJsonFragment(['name' => 'Tech', 'slug' => 'tech']);
    
    $responseWithImage->assertJsonStructure(["data" => ['category_image']]);
             
});

it('can show a category', function () {
    $category = Category::factory()->create();
    $response = $this->getJson("/api/categories/{$category->slug}");

    $response->assertStatus(200)
             ->assertJsonFragment(['id' => $category->id, 'name' => $category->name, 'slug' => $category->slug]);
});

it('can update a category', function () {
    $category = Category::factory()->create();
    $data = ['name' => 'Updated Name', 'description' => 'Updated Description', 'slug' => 'updated-name'];

    $response = $this->putJson("/api/categories/{$category->slug}", $data);

    $response->assertStatus(200)
             ->assertJsonFragment(['name' => 'Updated Name', 'slug' => 'updated-name', 'description' => 'Updated Description']);
});

it('can delete a category', function () {
    $category = Category::factory()->create();

    $response = $this->deleteJson("/api/categories/{$category->slug}");

    $response->assertStatus(200)
             ->assertJson(['success' => true, 'message' => 'Category deleted successfully.']);
});


