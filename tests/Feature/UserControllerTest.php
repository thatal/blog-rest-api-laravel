<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can register a new user', function () {
    $data = [
        'name' => 'Test User',
        'email' => 'testuser@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $response = $this->postJson('/api/register', $data);

    $response->assertStatus(201)
             ->assertJsonStructure(['access_token', 'token_type'])
             ->assertJsonFragment(['token_type' => 'Bearer']);
});

it('cannot register a user with an existing email', function () {
    $existingUser = User::factory()->create(['email' => 'testuser@example.com']);

    $data = [
        'name' => 'Test User',
        'email' => 'testuser@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $response = $this->postJson('/api/register', $data);

    $response->assertStatus(422)
             ->assertJson([
                 'message' => 'The email has already been taken.',
                 'errors' => [
                     'email' => ['The email has already been taken.'],
                 ],
             ]);
});

it('can get user details', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/user');

    $response->assertStatus(200)
             ->assertJson([
                 'id' => $user->id,
                 'name' => $user->name,
                 'email' => $user->email,
             ]);
});



it('can login a user', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);

    $data = ['email' => $user->email, 'password' => 'password'];

    $response = $this->postJson('/api/login', $data);

    $response->assertStatus(200)
             ->assertJsonStructure(['access_token', 'token_type'])
             ->assertJsonFragment(['token_type' => 'Bearer']);
});

it('can logout a user', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/logout');

    $response->assertStatus(200)
             ->assertJson(['message' => 'Successfully logged out']);
});

