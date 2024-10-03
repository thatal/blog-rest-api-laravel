<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user, ['*']);
});

it('can retrieve all roles', function () {
    $faker = \Faker\Factory::create();
    for ($i = 0; $i < 10; $i++) {
        Role::create(['name' => $faker->unique()->jobTitle, 'guard_name' => 'web']);
    }

    $response = $this->getJson('/api/roles');

    $response->assertStatus(200)
             ->assertJsonCount(10);
});

it('can create a role', function () {
    $data = ['name' => 'editor'];

    $response = $this->postJson('/api/roles', $data);

    $response->assertStatus(201)
             ->assertJsonFragment(['name' => 'editor']);
});

it('can retrieve all permissions', function () {
    $faker = \Faker\Factory::create();
    for ($i = 0; $i < 10; $i++) {
        Permission::create(['name' => $faker->unique()->word, 'guard_name' => 'web']);
    }

    $response = $this->getJson('/api/permissions');

    $response->assertStatus(200)
             ->assertJsonCount(10);
});

it('can assign a role to a user', function () {
    // $role = Role::factory()->create(['name' => 'editor']);
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    // create permissions
    $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);
    $this->user->assignRole($role);

    // update cache to know about the newly created permissions (required if using WithoutModelEvents in seeders)
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    $response = $this->getJson("/api/users/{$this->user->id}/roles", [
        'role_id' => $role->id
    ]);

    $response->assertStatus(200)
             ->assertJsonFragment(['name' => 'editor']);
});

it('can assign a permission to a role', function () {
    // $role = Role::factory()->create(['name' => 'admin']);
    // $permission = Permission::factory()->create(['name' => 'edit-posts']);
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $permission = Permission::create(['name' => 'edit-posts', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);



    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    $response = $this->getJson("/api/roles/{$role->id}/permissions");

    $response->assertStatus(200)
             ->assertJsonFragment(['name' => 'edit-posts']);
});

it('can check if a user has a role', function () {
    $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);
    $this->user->assignRole($role);

    $this->assertTrue($this->user->hasRole('editor'));
});

it('can check if a role has a permission', function () {
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $permission = Permission::create(['name' => 'edit-posts', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);

    $this->assertTrue($role->hasPermissionTo('edit-posts'));
});

