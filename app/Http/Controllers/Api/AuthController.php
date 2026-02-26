<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['errors' => ['login' => 'Email atau password salah']], 401);
        }

        $user->api_token = Str::random(60);
        $user->save();

        return response()->json($user);
    }

    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['errors' => ['login' => 'Email atau password salah']], 401);
        }

        if ($user->role !== 'admin') {
            return response()->json(['errors' => ['login' => 'Akun ini bukan admin']], 403);
        }

        $user->api_token = Str::random(60);
        $user->save();

        return response()->json($user);
    }

    public function verifyToken(Request $request)
    {
        $request->validate(['api_token' => 'required']);

        $user = User::where('api_token', $request->api_token)->first();

        if (!$user) {
            return response()->json(['errors' => ['token' => 'Token tidak valid']], 401);
        }

        return response()->json($user);
    }

    public function logout(Request $request)
    {
        $header = $request->header('Authorization');
        if ($header) {
            $token = str_replace('Token ', '', $header);
            $user = User::where('api_token', $token)->first();
            if ($user) {
                $user->api_token = null;
                $user->save();
            }
        }
        return response()->json(['message' => 'Logout berhasil']);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'api_token' => Str::random(60),
            'role'      => 'user',
        ]);

        return response()->json($user);
    }

    public function updateProfile(Request $request)
    {
        $header = $request->header('Authorization');
        if (!$header) {
            return response()->json(['errors' => ['auth' => 'Unauthorized']], 401);
        }

        $token = str_replace('Token ', '', $header);
        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json(['errors' => ['auth' => 'Unauthorized']], 401);
        }

        $request->validate([
            'name'  => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'bio'   => 'sometimes|string|max:500',
        ]);

        $user->update($request->only(['name', 'phone', 'bio']));

        return response()->json($user);
    }

    public function uploadAvatar(Request $request)
    {
        $header = $request->header('Authorization');
        if (!$header) {
            return response()->json(['errors' => ['auth' => 'Unauthorized']], 401);
        }

        $token = str_replace('Token ', '', $header);
        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json(['errors' => ['auth' => 'Unauthorized']], 401);
        }

        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return response()->json([
            'message'    => 'Avatar berhasil diupload',
            'avatar'     => $user->avatar,
            'avatar_url' => asset('storage/' . $user->avatar),
            'user'       => $user,
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Link reset password telah dikirim ke email kamu.'])
            : response()->json(['message' => 'Email tidak ditemukan.'], 404);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password'  => Hash::make($password),
                    'api_token' => Str::random(60),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password berhasil direset! Silakan login kembali.'])
            : response()->json(['message' => 'Token tidak valid atau sudah expired.'], 400);
    }
}