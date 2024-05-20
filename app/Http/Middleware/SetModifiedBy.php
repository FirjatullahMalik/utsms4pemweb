<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetModifiedBy
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('put') || $request->isMethod('patch')) {
            if (Auth::check()) {
                $user = Auth::user();
                $request->merge(['modified_by' => $user->name]); // Atau $user->id jika ingin menggunakan ID
            }
        }
        return $next($request);
    }
}

