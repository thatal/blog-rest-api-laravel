<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewCommentNotification;

class CommentController extends Controller
{
    public function index($postId)
    {
        // Retrieve all comments for a post
        $post = Post::findOrFail($postId);
        $comments = $post->comments()->with('user')->paginate(10);

        return response()->json([
            'success' => true, 
            'data' => $comments->items(),
            'links' => [
                'first' => $comments->url(1),
                'last' => $comments->url($comments->lastPage()),
                'prev' => $comments->previousPageUrl(),
                'next' => $comments->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ],
        ]);
    }

    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $post = Post::findOrFail($postId);
        $comment = new Comment([
            'content' => $request->content,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
        ]);

        $post->comments()->save($comment);
        $comment->post->user->notify(new NewCommentNotification($comment));

        return response()->json(['success' => true, 'data' => $comment], 201);
    }

    public function show($postId, $commentId)
    {
        $post = Post::findOrFail($postId);
        $comment = $post->comments()->with('user')->findOrFail($commentId);

        return response()->json(['success' => true, 'data' => $comment]);
    }

    public function update(Request $request, $postId, $commentId)
    {
        $request->validate(['content' => 'required|string']);

        $post = Post::findOrFail($postId);
        $comment = $post->comments()->findOrFail($commentId);

        if ($comment->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $comment->update(['content' => $request->content]);

        return response()->json(['success' => true, 'data' => $comment]);
    }

    public function destroy($postId, $commentId)
    {
        $post = Post::findOrFail($postId);
        $comment = $post->comments()->findOrFail($commentId);

        if ($comment->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['success' => true, 'message' => 'Comment deleted successfully.']);
    }
}
