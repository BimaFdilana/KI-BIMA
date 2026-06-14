<?php

namespace App\Http\Middleware;

use App\Models\Auth\UserDeviceModel;
use App\Services\Message\VerificationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class EnsureDeviceIsVerifiedApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    private function generateDeviceId(Request $request, Agent $agent): string
    {
        $components = [
            $agent->device() ?: 'unknown',
            $agent->platform() ?: 'unknown',
            $request->ip() ?: 'unknown',
            $request->userAgent() ?: 'unknown'
        ];

        return hash('sha256', implode('|', $components));
    }


    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terotentikasi',
            ], 401);
        }

        // First check if phone is verified (jika diperlukan)
        if (!$user->isPhoneVerified()) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor telepon tidak terverifikasi',
                'requires_verification' => 'phone'
            ], 403);
        }

        // Generate device ID dari request
        $agent = new Agent();
        $deviceId = $this->generateDeviceId($request, $agent);

        // Check if device ID is provided in header (untuk konsistensi perangkat)
        if ($request->header('X-Device-ID')) {
            $deviceId = $request->header('X-Device-ID');
        }

        $verificationService = app(VerificationService::class);

        // Check if this device is remembered for the user
        if (!$verificationService->isDeviceRemembered($user, $deviceId)) {
            // Device not remembered, require verification
            return response()->json([
                'success' => false,
                'message' => 'Perangkat tidak terverifikasi',
                'requires_verification' => 'device',
                'device_id' => $deviceId,
                'ip_address' => $request->ip()
            ], 403);
        }

        $updateActive = UserDeviceModel::updateOrCreate(
            ['user_id' => $user->id, 'device_id' => $deviceId],
            [
                'last_active_at' => now(),
            ]
        );

        return $next($request);
    }
}
