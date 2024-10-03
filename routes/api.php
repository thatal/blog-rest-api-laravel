<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolePermissionController;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Role permissions Routes
    Route::get('roles', [RolePermissionController::class, 'roles']);
    Route::get('permissions', [RolePermissionController::class, 'permissions']);
    Route::get('roles/{role_id}/permissions', [UserController::class, 'rolePermissions']);
    Route::post('roles', [RolePermissionController::class, 'storeRole']);
    // User Routes
    Route::get('user', function (Request $request) {
        return $request->user();
    });
    Route::get('users/{user_id}/roles', [UserController::class, 'roles']);
    Route::get('users/{user_id}/permissions', [UserController::class, 'permissions']);
    

    // Post Routes
    Route::apiResource('posts', PostController::class);
    // Category Routes
    Route::apiResource('categories', CategoryController::class);

    // Comment Routes
    Route::get('posts/{postId}/comments', [CommentController::class, 'index']);
    Route::post('posts/{postId}/comments', [CommentController::class, 'store']);
    Route::get('posts/{postId}/comments/{commentId}', [CommentController::class, 'show']);
    Route::put('posts/{postId}/comments/{commentId}', [CommentController::class, 'update']);
    Route::delete('posts/{postId}/comments/{commentId}', [CommentController::class, 'destroy']);
});

