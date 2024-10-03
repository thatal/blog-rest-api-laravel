<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function roles ($user_id, Request $request) {
        $roles = User::findOrFail($user_id)->roles;
        return response()->json(['success' => true, 'data' => $roles]);
    }

    public function assignRole ($user_id, Request $request) {
        $user = User::findOrFail($user_id);
        $user->roles()->attach($request->role_id);
        return response()->json(['success' => true, 'data' => $user->roles]);
    }

    public function removeRole ($user_id, Request $request) {
        $user = User::findOrFail($user_id);
        $user->roles()->detach($request->role_id);
        return response()->json(['success' => true, 'data' => $user->roles]);
    }

    public function permissions ($user_id, Request $request) {
        $permissions = User::findOrFail($user_id)->permissions;
        return response()->json(['success' => true, 'data' => $permissions]);
    }

    public function assignPermission ($user_id, Request $request) {
        $user = User::findOrFail($user_id);
        $user->permissions()->attach($request->permission_id);
        return response()->json(['success' => true, 'data' => $user->permissions]);
    }

    public function removePermission ($user_id, Request $request) {
        $user = User::findOrFail($user_id);
        $user->permissions()->detach($request->permission_id);
        return response()->json(['success' => true, 'data' => $user->permissions]);
    }

    public function index()
    {
        $users = User::paginate(10);
        return response()->json(['success' => true, 'data' => $users]);
    }

    public function rolePermissions ($role_id, Request $request) {
        $role = Role::findOrFail($role_id);
        $permissions = $role->permissions;
        return response()->json(['success' => true, 'data' => $permissions]);
    }
}
