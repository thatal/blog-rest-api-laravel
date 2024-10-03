<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(function () {
    // Create a user and authenticate using Sanctum
    $this->user = User::factory()->create();
    $this->post = Post::factory()->create();
    Sanctum::actingAs($this->user);
});

it('can retrieve all comments for a post', function () {
    Comment::factory(10)->create(['post_id' => $this->post->id]);

    $response = $this->getJson("/api/posts/{$this->post->id}/comments");

    $response->assertStatus(200)
             ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => ['id', 'content', 'user_id', 'post_id', 'created_at', 'updated_at'],
                    ],
                    'links',
                    'meta'
                ]);
});

it('can create a comment for a post', function () {
    $data = ['content' => 'This is a test comment'];

    $response = $this->postJson("/api/posts/{$this->post->id}/comments", $data);

    $response->assertStatus(201)
             ->assertJsonFragment(['content' => 'This is a test comment']);
});

it('can show a comment for a post', function () {
    $comment = Comment::factory()->create(['post_id' => $this->post->id]);

    $response = $this->getJson("/api/posts/{$this->post->id}/comments/{$comment->id}");

    $response->assertStatus(200)
             ->assertJsonFragment(['id' => $comment->id, 'content' => $comment->content]);
});

it('can update a comment for a post', function () {
    $comment = Comment::factory()->create(['post_id' => $this->post->id, 'user_id' => $this->user->id]);
    $data = ['content' => 'Updated comment content'];

    $response = $this->putJson("/api/posts/{$this->post->id}/comments/{$comment->id}", $data);

    $response->assertStatus(200)
             ->assertJsonFragment(['content' => 'Updated comment content']);
});

it('can delete a comment for a post', function () {
    $comment = Comment::factory()->create(['post_id' => $this->post->id, 'user_id' => $this->user->id]);

    $response = $this->deleteJson("/api/posts/{$this->post->id}/comments/{$comment->id}");

    $response->assertStatus(200)
             ->assertJson(['success' => true, 'message' => 'Comment deleted successfully.']);
});
