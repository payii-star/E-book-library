<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReadingHistory;
use App\Models\User;
use Illuminate\Http\Request;

class ReadingHistoryController extends Controller
{
    public function index(Request $request)
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $user  = User::where('api_token', $token)->first();

        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $histories = ReadingHistory::with(['book', 'book.category', 'chapter'])
            ->where('user_id', $user->id)
            ->orderBy('read_at', 'desc')
            ->get()
            ->unique('book_id')
            ->take(6)
            ->values();

        return response()->json($histories);
    }

    public function store(Request $request)
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $user  = User::where('api_token', $token)->first();

        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $request->validate([
            'book_id'    => 'required|exists:books,id',
            'chapter_id' => 'required|exists:chapters,id',
        ]);

        ReadingHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'book_id' => $request->book_id,
            ],
            [
                'chapter_id' => $request->chapter_id,
                'read_at'    => now(),
            ]
        );

        return response()->json(['message' => 'Reading history saved']);
    }
}