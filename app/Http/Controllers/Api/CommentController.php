<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // GET /api/books/{bookId}/comments
    public function index(Request $request, $bookId)
    {
        $query = Comment::with('user:id,name,avatar')
            ->where('book_id', $bookId);

        if ($request->chapter_id) {
            $query->where('chapter_id', $request->chapter_id);
        }

        return response()->json($query->orderByDesc('created_at')->paginate(15));
    }

    // POST /api/books/{bookId}/comments
    public function store(Request $request, $bookId)
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $user  = User::where('api_token', $token)->first();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        $request->validate([
            'content'    => 'required|string|max:1000',
            'chapter_id' => 'nullable|exists:chapters,id',
        ]);

        $comment = Comment::create([
            'user_id'    => $user->id,
            'book_id'    => $bookId,
            'chapter_id' => $request->chapter_id,
            'content'    => $request->content,
        ]);

        return response()->json($comment->load('user:id,name,avatar'), 201);
    }

    // DELETE /api/comments/{id}
    public function destroy(Request $request, $id)
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $user  = User::where('api_token', $token)->first();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        $comment = Comment::findOrFail($id);
        if ($comment->user_id !== $user->id && $user->role !== 'admin') abort(403);

        $comment->delete();
        return response()->json(['message' => 'Komentar dihapus']);
    }
}