<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Comment;
use App\Models\ReadingHistory;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    // GET /api/admin/statistics
    public function index(Request $request)
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $user  = User::where('api_token', $token)->first();
        if (!$user || $user->role !== 'admin') abort(403);

        return response()->json([
            'total_books'     => Book::count(),
            'total_users'     => User::where('role', 'user')->count(),
            'total_reads'     => ReadingHistory::count(),
            'total_reviews'   => Review::count(),
            'total_comments'  => Comment::count(),
            'most_read_books' => Book::orderByDesc('total_views')
                                     ->limit(5)
                                     ->get(['id','title','author','total_views','cover_image']),
            'top_rated_books' => Book::withAvg('reviews', 'rating')
                                     ->orderByDesc('reviews_avg_rating')
                                     ->limit(5)
                                     ->get(['id','title','author','cover_image']),
            'recent_reads'    => ReadingHistory::with([
                                    'user:id,name',
                                    'book:id,title',
                                    'chapter:id,title,chapter_number',
                                 ])
                                 ->orderByDesc('read_at')
                                 ->limit(10)
                                 ->get(),
        ]);
    }
}