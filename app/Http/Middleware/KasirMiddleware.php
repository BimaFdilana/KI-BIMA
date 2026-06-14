<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KasirMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Check if user has 'kasir' role OR is the owner of a toko
        $hasKasirRole = $user->hasRole('kasir');
        
        if (!$hasKasirRole) {
            return response()->json(['success' => false, 'message' => 'Anda bukan kasir atau owner.'], 403);
        }

        return $next($request);
    }
}
