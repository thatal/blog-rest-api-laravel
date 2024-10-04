<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user', 'categories', 'media')
        ->when(request("category"), function($query) {
            return $query->whereHas('categories', function($query) {
                $query->whereRaw('LOWER(name) = ?', [strtolower(request('category'))]);
            });
        })
        ->when(request("search"), function($query) {
            return $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower(request('search')) . '%']);
        })
        ->withCount('comments')
        ->latest()
        ->paginate(10); // Eager load user and categories

        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\PostResource::collection($posts->items()), // Use PostResource for the items
            'links' => [
                'first' => $posts->url(1),
                'last' => $posts->url($posts->lastPage()),
                'prev' => $posts->previousPageUrl(),
                'next' => $posts->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'categories' => 'array',
            'feature_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $post = auth()->user()->posts()->create($validated);
        if ($request->hasFile('feature_image')) {
            $post->addMediaFromRequest('feature_image')->toMediaCollection('feature_images');
        }
        $post->categories()->sync($request->categories);
        $post->load('user', 'categories', 'media')->loadCount('comments');

        return response()->json(new \App\Http\Resources\PostResource($post), 201);
    }

    public function show(Post $post)
    {
        $post->load(['user', 'categories', 'comments'])->loadCount('comments');
        return response()->json(new \App\Http\Resources\PostResource($post));
    }

    public function update(Request $request, Post $post)
    {
        // $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'content' => 'string',
            'categories' => 'array',
            'feature_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('feature_image')) {
            $post->addMediaFromRequest('feature_image')->toMediaCollection('feature_images');
        }

        $post->update($validated);
        $post->categories()->sync($request->categories);
        $post->load('user', 'categories', 'media')->loadCount('comments');
        return response()->json(new \App\Http\Resources\PostResource($post));
    }

    public function destroy(Post $post)
    {
        // $this->authorize('delete', $post);

        $post->delete();
        return response()->json([
            'success' => true, 
            'message' => 'Post deleted successfully.'
        ], 200);
    }

}
