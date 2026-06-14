<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureKasirRole
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->hasRole('kasir')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke fitur ini. Hanya kasir yang dapat mengakses.',
            ], 403);
        }

        return $next($request);
    }
}
