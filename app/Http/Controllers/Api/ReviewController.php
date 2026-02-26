<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // GET /api/books/{bookId}/reviews
    public function index($bookId)
    {
        $reviews = Review::with('user:id,name,avatar')
            ->where('book_id', $bookId)
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json($reviews);
    }

    // POST /api/books/{bookId}/reviews
    public function store(Request $request, $bookId)
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $user  = User::where('api_token', $token)->first();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        Book::findOrFail($bookId);

        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = Review::updateOrCreate(
            ['user_id' => $user->id, 'book_id' => $bookId],
            ['rating' => $request->rating, 'comment' => $request->comment]
        );

        return response()->json($review->load('user:id,name,avatar'), 201);
    }

    // DELETE /api/reviews/{id}
    public function destroy(Request $request, $id)
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $user  = User::where('api_token', $token)->first();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        $review = Review::findOrFail($id);
        if ($review->user_id !== $user->id && $user->role !== 'admin') abort(403);

        $review->delete();
        return response()->json(['message' => 'Review dihapus']);
    }
}