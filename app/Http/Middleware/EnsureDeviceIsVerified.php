<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class EnsureDeviceIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // First check if phone is verified
        if (!$user->isPhoneVerified()) {
            return redirect()->route('verification.phone');
        }

        // Get device ID from session (how it was stored during login)
        $deviceId = session()->get('device_id');

        // If session doesn't have device ID, generate it (as a fallback)
        if (!$deviceId) {
            $agent = new Agent();
            $deviceId = md5($agent->device() . $agent->platform() . $request->ip());
            session()->put('device_id', $deviceId);
        }

        // Check if this device exists for the user
        $device = $user->deviceVerifications()->where('device_id', $deviceId)->first();
        if (!$device) {
            // Device doesn't exist in database for this user, log them out
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->with('message', 'Unrecognized device. Please log in again.');
        }

        // Device is verified, update last_active_at
        $device->update(['last_active_at' => now()]);
        return $next($request);
    }
}
