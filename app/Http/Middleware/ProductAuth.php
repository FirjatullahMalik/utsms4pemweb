<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Firebase\JWT\Key;

class ProductAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $jwt = $request->bearerToken();

        if($jwt == 'null' || $jwt == '') {
            return response()->json(['error' => 'Token not found'], 401);
        } else {
            
            $jwtDecoded = JWT::decode($jwt, new Key(env('JWT_SECRET'), 'HS256'));

            if($jwtDecoded->role == 'admin' || $jwtDecoded->role == 'user' ) {
                return $next($request);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

        }
    }
}

