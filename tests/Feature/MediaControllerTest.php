<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can upload media', function () {
    $response = $this->actingAs($this->user, 'sanctum')->post('/api/media/upload', [
        'file' => \Illuminate\Http\UploadedFile::fake()->image('test.jpg'),
    ]);

    $response->assertStatus(201);
    $this->assertFileExists(storage_path('app/public/' . $response->json('real_path')));
    $response->assertJsonStructure(['url', 'real_path', 'size', 'mime_type', 'original_name']);
});

it('can delete media', function () {
    // First, upload media
    $uploadResponse = $this->actingAs($this->user, 'sanctum')->post('/api/media/upload', [
        'file' => \Illuminate\Http\UploadedFile::fake()->image('test.jpg'),
    ]);

    $uploadResponse->assertStatus(201);
    $uploadResponse->assertJsonStructure(['real_path']);

    $mediaPath = $uploadResponse->json('real_path');

    // Then, delete the uploaded media using the URL
    $deleteResponse = $this->actingAs($this->user, 'sanctum')->delete("/api/media", [
        'path' => $mediaPath,
    ]);
    $deleteResponse->assertStatus(200);
    $this->assertFileDoesNotExist(storage_path('app/public/' . $mediaPath));
    $deleteResponse->assertJson(['message' => 'Media deleted successfully.']);
});

// it('returns a 404 when deleting non-existent media', function () {
//     $response = $this->actingAs($this->user, 'sanctum')->delete('/api/media/9999');

//     $response->assertStatus(404);
//     $response->assertJson(['message' => 'Media not found.']);
// });
