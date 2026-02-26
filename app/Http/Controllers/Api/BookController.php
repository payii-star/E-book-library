<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    private function getAdmin(Request $request)
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $user  = User::where('api_token', $token)->first();
        if (!$user || $user->role !== 'admin') abort(403, 'Unauthorized');
        return $user;
    }

    // GET /api/books — public list
    public function index(Request $request)
    {
        $query = Book::with('category')
            ->withCount(['reviews', 'chapters'])  // ← fix: tambah chapters
            ->withAvg('reviews', 'rating')
            ->where('status', 'published');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title',  'like', '%' . $request->search . '%')
                  ->orWhere('author', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->category_id) $query->where('category_id', $request->category_id);

        match($request->sort ?? 'latest') {
            'popular' => $query->orderByDesc('total_views'),
            'rating'  => $query->orderByDesc('reviews_avg_rating'),
            'title'   => $query->orderBy('title'),
            default   => $query->orderByDesc('created_at'),
        };

        return response()->json($query->paginate($request->per_page ?? 12));
    }

    // GET /api/books/{slug} — public detail
    public function show($slug)
    {
        $book = Book::with([
                'category',
                'chapters' => fn($q) => $q->select('id','book_id','chapter_number','title','read_count')
            ])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $book->increment('total_views');
        return response()->json($book);
    }

    // GET /api/admin/books — admin list semua status
    public function adminIndex(Request $request)
    {
        $this->getAdmin($request);

        $query = Book::with('category')
            ->withCount(['reviews', 'chapters'])
            ->withAvg('reviews', 'rating');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title',  'like', '%' . $request->search . '%')
                  ->orWhere('author', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->category_id) $query->where('category_id', $request->category_id);
        if ($request->status)      $query->where('status', $request->status);

        return response()->json(
            $query->orderByDesc('created_at')->paginate($request->per_page ?? 10)
        );
    }

    // GET /api/admin/books/{id} — detail untuk edit
    public function adminShow(Request $request, $id)
    {
        $this->getAdmin($request);
        return response()->json(Book::with('chapters')->findOrFail($id));
    }

    // POST /api/admin/books — tambah buku
    public function store(Request $request)
    {
        $this->getAdmin($request);

        $request->validate([
            'title'          => 'required|string|max:255',
            'author'         => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'synopsis'       => 'nullable|string',
            'publisher'      => 'nullable|string|max:255',
            'published_year' => 'nullable|integer|min:1000|max:2099',
            'isbn'           => 'nullable|string|unique:books,isbn',
            'status'         => 'in:published,draft',
            'cover_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data         = $request->except('cover_image');
        $data['slug'] = Str::slug($request->title) . '-' . Str::random(5);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        return response()->json(Book::create($data)->load('category'), 201);
    }

    // POST /api/admin/books/{id} — update buku
    public function update(Request $request, $id)
    {
        $this->getAdmin($request);
        $book = Book::findOrFail($id);

        $request->validate([
            'title'          => 'sometimes|string|max:255',
            'author'         => 'sometimes|string|max:255',
            'category_id'    => 'sometimes|exists:categories,id',
            'synopsis'       => 'nullable|string',
            'publisher'      => 'nullable|string|max:255',
            'published_year' => 'nullable|integer',
            'isbn'           => 'nullable|string|unique:books,isbn,' . $id,
            'status'         => 'in:published,draft',
            'cover_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except('cover_image');

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) Storage::disk('public')->delete($book->cover_image);
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $book->update($data);
        return response()->json($book->load('category'));
    }

    // DELETE /api/admin/books/{id}
    public function destroy(Request $request, $id)
    {
        $this->getAdmin($request);
        $book = Book::findOrFail($id);
        if ($book->cover_image) Storage::disk('public')->delete($book->cover_image);
        $book->delete();
        return response()->json(['message' => 'Buku berhasil dihapus']);
    }
}