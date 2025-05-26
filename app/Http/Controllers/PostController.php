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
        ->when(request("tags"), function($query) {
            $tags = explode(',', request('tags'));
            return $query->whereHas('tags', function($query) use ($tags) {
                $query->whereIn('name', array_map('trim', $tags));
            });
        })
        ->with('tags') // Eager load tags
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
            'slug' => 'required|string|max:255|unique:posts,slug',
            'content' => 'required',
            'categories' => 'array',
            'feature_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tags' => 'array',
            'tags.*' => 'string|max:255',
        ]);
        \DB::beginTransaction();
        try {
            $tags = $request->input('tags', []);
            $tagIds = [];
            foreach ($tags as $tagName) {
                $tag = \App\Models\Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $post = auth()->user()->posts()->create($validated);

            if ($request->hasFile('feature_image')) {
                $post->addMediaFromRequest('feature_image')->toMediaCollection('feature_images');
            }

            $post->categories()->sync($request->categories);
            $post->tags()->sync($tagIds);
            $post->load('user', 'categories', 'media', 'tags')->loadCount('comments');
            // Sync tags
            \DB::commit();
            return response()->json(new \App\Http\Resources\PostResource($post), 201);
        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json(['error' => 'Post creation failed.'], 500);
        }
    }

    public function show(Post $post)
    {
        $post->load(['user', 'categories', 'comments', 'tags'])->loadCount('comments');
        return response()->json(new \App\Http\Resources\PostResource($post));
    }

    public function update(Request $request, Post $post)
    {
        // $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'slug' => 'string|max:255|unique:posts,slug,' . $post->id,
            'content' => 'string',
            'categories' => 'array',
            'feature_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tags' => 'array',
            'tags.*' => 'string|max:255',
        ]);

        if ($request->hasFile('feature_image')) {
            $post->addMediaFromRequest('feature_image')->toMediaCollection('feature_images');
        }
        
        $post->update($validated);
        if ($request->has('tags')) {
            $tagIds = [];
            foreach ($request->input('tags') as $tagName) {
                $tag = \App\Models\Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        }
        $post->categories()->sync($request->categories);
        $post->load('user', 'categories', 'media', 'tags')->loadCount('comments');
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
