<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        // Retrieve all categories with pagination
        $categories = Category::withCount('posts')->paginate(10);
        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories->items()),
            "links" => [
                "first" => $categories->url(1),
                "last" => $categories->url($categories->lastPage()),
                "prev" => $categories->previousPageUrl(),
                "next" => $categories->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $categories['current_page'],
                'last_page' => $categories['last_page'],
                'per_page' => $categories['per_page'],
                'total' => $categories['total'],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:categories',
            'description' => 'nullable|string',
            'category_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $category = Category::create($validated);
        if ($request->hasFile('category_image')) {
            $category->addMediaFromRequest('category_image')->toMediaCollection('category_images');
        }
        $category->load('media');
        return response()->json(['success' => true, 'data' => new CategoryResource($category)], 201);
    }

    public function show($id)
    {
        $category = Category::with('posts')->findOrFail($id);
        return response()->json(['success' => true, 'data' => new CategoryResource($category)]);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'category_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $category->update($request->all());
        if ($request->hasFile('category_image')) {
            $category->addMediaFromRequest('category_image')->toMediaCollection('category_images');
        }

        return response()->json(['success' => true, 'data' => new CategoryResource($category)]);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['success' => true, 'message' => 'Category deleted successfully.']);
    }
}