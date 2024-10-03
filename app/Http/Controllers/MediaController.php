<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class MediaController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
        ]);
        $file = $request->file('file');

        // Generate a unique file name
        $fileName = time() . '_' . \Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

        // Store the file in the public directory inside the file-manager folder
        $filePath = $file->storeAs('file-manager', $fileName, 'public');

        return response()->json([
            'url' => asset('storage/' . $filePath),
            'real_path' => $filePath,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'original_name' => $file->getClientOriginalName(),
        ], 201);
    }

    public function delete(Request $request)
    {
        // Check if the file exists in the Post content column
        $postWithMedia = Post::where('content', 'like', '%' . $request->input('path') . '%')->first();

        if ($postWithMedia) {
            return response()->json(['error' => 'File is used in a post and cannot be deleted.'], 400);
        }
        // TODO: need to add permission to delete media
        $request->validate([
            'path' => 'required|string',
        ]);
        $filePath = $request->input('path');

        if (\Storage::disk('public')->exists($filePath)) {
            \Storage::disk('public')->delete($filePath);
        } else {
            return response()->json(['error' => 'File not found.'], 404);
        }

        return response()->json(['message' => 'Media deleted successfully.'], 200);
    }
}
