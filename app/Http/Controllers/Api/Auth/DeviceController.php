<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\Auth\UserDeviceModel;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function registerFcmToken(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string',
            'fcm_token' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        UserDeviceModel::registerDevice(
            userId: auth()->id(),
            deviceId: $validated['device_id'],
            ipAddress: $request->ip(),
            fcmToken: $validated['fcm_token'],
            deviceName: $validated['device_name'],
            userAgent: $request->userAgent()
        );

        return response()->json(['message' => 'FCM token registered successfully']);
    }

    public function unregisterDevice(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string',
        ]);

        UserDeviceModel::where('user_id', auth()->id())
            ->where('device_id', $validated['device_id'])
            ->delete();

        return response()->json(['message' => 'Device unregistered successfully']);
    }
}
