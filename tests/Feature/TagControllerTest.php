<?php


use App\Models\User;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{actingAs, getJson, postJson, putJson, deleteJson};

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user, 'sanctum');
});

it('can list tags', function () {
    Tag::factory()->count(3)->create();

    $response = getJson('/api/tags');

    $response->assertOk()
        ->assertJsonStructure(['data' => [['id', 'name']]]);
});

it('can create a tag', function () {
    $response = postJson('/api/tags', [
        'name' => 'Technology',
    ]);

    $response->assertCreated()
        ->assertJsonFragment(['name' => 'Technology']);

    expect(Tag::where('name', 'Technology')->exists())->toBeTrue();
});

it('validates tag creation', function () {
    $response = postJson('/api/tags', [
        'name' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('can show a tag', function () {
    $tag = Tag::factory()->create(['name' => 'Science']);

    $response = getJson("/api/tags/{$tag->id}");

    $response->assertOk()
        ->assertJsonFragment(['name' => 'Science']);
});

it('can update a tag', function () {
    $tag = Tag::factory()->create(['name' => 'OldName']);

    $response = putJson("/api/tags/{$tag->id}", [
        'name' => 'UpdatedTag',
    ]);

    $response->assertOk()
        ->assertJsonFragment(['name' => 'UpdatedTag']);
});

it('validates tag update', function () {
    $tag = Tag::factory()->create();

    $response = putJson("/api/tags/{$tag->id}", [
        'name' => '', // invalid
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('can delete a tag', function () {
    $tag = Tag::factory()->create();

    $response = deleteJson("/api/tags/{$tag->id}");

    $response->assertOk()
        ->assertJson(['message' => 'Tag deleted successfully.']);

    expect(Tag::find($tag->id))->toBeNull();
});

