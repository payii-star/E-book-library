<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    // GET /api/bookmarks
    public function index(Request $request)
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $user  = User::where('api_token', $token)->first();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        return response()->json(
            Bookmark::with([
                'book:id,title,slug,cover_image,author',
                'chapter:id,chapter_number,title'
            ])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get()
        );
    }

    // POST /api/bookmarks
    public function store(Request $request)
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $user  = User::where('api_token', $token)->first();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        $request->validate([
            'book_id'    => 'required|exists:books,id',
            'chapter_id' => 'required|exists:chapters,id',
        ]);

        $bookmark = Bookmark::updateOrCreate(
            ['user_id' => $user->id, 'book_id' => $request->book_id],
            ['chapter_id' => $request->chapter_id]
        );

        return response()->json($bookmark, 201);
    }

    // DELETE /api/bookmarks/{bookId}
    public function destroy(Request $request, $bookId)
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $user  = User::where('api_token', $token)->first();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        Bookmark::where('user_id', $user->id)->where('book_id', $bookId)->delete();
        return response()->json(['message' => 'Bookmark dihapus']);
    }
}