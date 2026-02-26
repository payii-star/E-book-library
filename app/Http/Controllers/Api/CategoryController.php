<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // GET /api/categories
    public function index()
    {
        return response()->json(Category::withCount('books')->get());
    }

    // POST /api/admin/categories
    public function store(Request $request)
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $user  = User::where('api_token', $token)->first();
        if (!$user || $user->role !== 'admin') abort(403);

        $request->validate([
            'name' => 'required|string|unique:categories,name',
        ]);

        $category = Category::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return response()->json($category, 201);
    }

    // DELETE /api/admin/categories/{id}
    public function destroy(Request $request, $id)
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $user  = User::where('api_token', $token)->first();
        if (!$user || $user->role !== 'admin') abort(403);

        Category::findOrFail($id)->delete();
        return response()->json(['message' => 'Kategori berhasil dihapus']);
    }
}