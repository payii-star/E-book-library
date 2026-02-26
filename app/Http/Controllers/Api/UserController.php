<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // GET /api/admin/users
    public function index(Request $request)
    {
        $token = str_replace('Token ', '', $request->header('Authorization'));
        $user  = User::where('api_token', $token)->first();
        if (!$user || $user->role !== 'admin') abort(403);

        $query = User::where('role', 'user');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name',  'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        return response()->json(
            $query->orderByDesc('created_at')->paginate($request->per_page ?? 15)
        );
    }
}