<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Firebase\JWT\JWT;

class OAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
    
        // Cek apakah pengguna sudah terdaftar dalam basis data berdasarkan email
        $user = User::where('email', $googleUser->email)->first();
    
        if ($user) {
            // Jika pengguna sudah terdaftar, buat sesi atau token otentikasi untuk pengguna
            Auth::login($user);
        } else {
            // Jika pengguna belum terdaftar, buat pengguna baru di basis data
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => bcrypt(Str::random(16)),
            ]);
    
            // Login pengguna baru
            Auth::login($user);
        }

        // Buat token JWT
        $payload = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'iat' => now()->timestamp,
            'exp' => now()->timestamp + 7200
        ];

        $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        return response()->json([
            'status' => 'success',
            'token' => "Bearer {$token}",
            'user' => $user
        ], 200);
    }
}
