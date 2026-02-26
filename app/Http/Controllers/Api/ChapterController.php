<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\ReadingHistory;
use App\Models\User;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    // GET /api/books/{slug}/chapters/{number}
    public function show(Request $request, $slug, $number)
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $user  = User::where('api_token', $token)->first();
        if (!$user) return response()->json(['error' => 'Login dulu untuk membaca buku'], 401);

        $book    = Book::where('slug', $slug)->where('status', 'published')->firstOrFail();
        $chapter = Chapter::where('book_id', $book->id)
                          ->where('chapter_number', $number)
                          ->firstOrFail();

        $chapter->increment('read_count');

        ReadingHistory::updateOrCreate(
            ['user_id' => $user->id, 'book_id' => $book->id, 'chapter_id' => $chapter->id],
            ['read_at' => now()]
        );

        $prev = Chapter::where('book_id', $book->id)
                       ->where('chapter_number', $number - 1)
                       ->select('id','chapter_number','title')
                       ->first();

        $next = Chapter::where('book_id', $book->id)
                       ->where('chapter_number', $number + 1)
                       ->select('id','chapter_number','title')
                       ->first();

        return response()->json([
            'book'    => $book->only(['id','title','slug','author']),
            'chapter' => $chapter,
            'prev'    => $prev,
            'next'    => $next,
        ]);
    }

    // POST /api/admin/books/{bookId}/chapters
    public function store(Request $request, $bookId)
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $user  = User::where('api_token', $token)->first();
        if (!$user || $user->role !== 'admin') abort(403);

        $book = Book::findOrFail($bookId);

        $request->validate([
            'chapter_number' => 'required|integer',
            'title'          => 'required|string|max:255',
            'content'        => 'required|string',
        ]);

        $exists = Chapter::where('book_id', $bookId)
                         ->where('chapter_number', $request->chapter_number)
                         ->exists();
        if ($exists) {
            return response()->json(['errors' => ['chapter_number' => 'Nomor bab sudah ada']], 422);
        }

        $chapter = Chapter::create([
            'book_id'        => $book->id,
            'chapter_number' => $request->chapter_number,
            'title'          => $request->title,
            'content'        => $request->content,
        ]);

        return response()->json($chapter, 201);
    }

    // PUT /api/admin/chapters/{id}
    public function update(Request $request, $id)
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $user  = User::where('api_token', $token)->first();
        if (!$user || $user->role !== 'admin') abort(403);

        $chapter = Chapter::findOrFail($id);

        $request->validate([
            'chapter_number' => 'sometimes|integer',
            'title'          => 'sometimes|string|max:255',
            'content'        => 'sometimes|string',
        ]);

        $chapter->update($request->only(['chapter_number', 'title', 'content']));
        return response()->json($chapter);
    }

    // DELETE /api/admin/chapters/{id}
    public function destroy(Request $request, $id)
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $user  = User::where('api_token', $token)->first();
        if (!$user || $user->role !== 'admin') abort(403);

        Chapter::findOrFail($id)->delete();
        return response()->json(['message' => 'Chapter berhasil dihapus']);
    }
}