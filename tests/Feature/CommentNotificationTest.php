<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewCommentNotification;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a user and authenticate using Sanctum
    $this->user = User::factory()->create();
    $this->post = Post::factory()->create();
    Sanctum::actingAs($this->user);
});

it('sends a notification when a comment is added to a post', function () {
    Notification::fake();

    $post = Post::factory()->create();
    $commentData = ['content' => 'Great Post!'];

    $response = $this->postJson("/api/posts/{$post->id}/comments", $commentData);

    $response->assertStatus(201);

    Notification::assertSentTo(
        [$post->user], NewCommentNotification::class
    );
});

