<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user', 'categories')
        ->when(request("category"), function($query) {
            return $query->whereHas('categories', function($query) {
                $query->whereRaw('LOWER(name) = ?', [strtolower(request('category'))]);
            });
        })
        ->when(request("search"), function($query) {
            return $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower(request('search')) . '%']);
        })
        ->latest()
        ->paginate(10); // Eager load user and categories

        return response()->json([
            'success' => true,
            'data' => $posts->items(), // Return the items in the paginated collection
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
        ]);

        $post = auth()->user()->posts()->create($validated);
        $post->categories()->sync($request->categories);

        return response()->json($post, 201);
    }

    public function show(Post $post)
    {
        return response()->json($post->load(['user', 'categories', 'comments']));
    }

    public function update(Request $request, Post $post)
    {
        // $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'content' => 'string',
        ]);

        $post->update($validated);
        return response()->json($post);
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
