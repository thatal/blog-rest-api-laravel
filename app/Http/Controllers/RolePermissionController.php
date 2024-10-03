<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class RolePermissionController extends Controller
{
    public function roles (Request $request) {
        $roles = \Spatie\Permission\Models\Role::all();
        return response()->json($roles);
    }

    public function permissions (Request $request) {
        $permissions = \Spatie\Permission\Models\Permission::all();
        return response()->json($permissions);
    }

    public function storeRole (Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles',
        ]);

        $role = \Spatie\Permission\Models\Role::create($validated);

        return response()->json($role, 201);
    }
}
