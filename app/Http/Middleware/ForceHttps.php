<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceHttps
{
    public function handle(Request $request, Closure $next)
    {
        if (config('app.env') === 'production' && !$request->isSecure()) {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}
