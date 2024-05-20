<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use Firebase\JWT\ExpiredException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:5',
            'role' => 'sometimes|string|in:admin,user' // Validasi untuk role
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->messages()
            ], 422);
        }

        $role = $request->input('role', 'user'); // Default ke 'user' jika tidak diisi

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash password
            'role' => $role, // Set role berdasarkan input atau default 'user'
        ]);

        $token = JWT::encode(['sub' => $user->id, 'iat' => now()->timestamp], env('JWT_SECRET'), 'HS256');

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => "Bearer {$token}"
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->messages()
            ], 422);
        }

        if (!Auth::attempt($validator->validated())) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $payload = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'iat' => now()->timestamp,
            'exp' => now()->timestamp + 7200 // Token expiration (2 hours)
        ];

        $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        Log::create([
            'module' => 'login',
            'action' => 'login akun',
            'useraccess' => $user->email
        ]);

        return response()->json([
            'status' => 'success',
            'token' => "Bearer {$token}",
            'user' => $user
        ], 200);
    }


}