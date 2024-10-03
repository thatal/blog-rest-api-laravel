<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Creating roles
        $admin = Role::create(['name' => 'Admin']);
        $editor = Role::create(['name' => 'Editor']);
        $viewer = Role::create(['name' => 'Viewer']);

        // Creating permissions
        $permissions = [
            'manage posts',
            'manage comments',
            'manage categories',
            'manage users'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assigning permissions to roles
        $admin->givePermissionTo($permissions);
        $editor->givePermissionTo(['manage posts', 'manage comments']);
        $viewer->givePermissionTo([]);
    }
}
