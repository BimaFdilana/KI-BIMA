<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EnsurePhoneIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user->isPhoneVerified()) {
            return redirect()->route('verification.phone');
        }

        return $next($request);
    }
}
