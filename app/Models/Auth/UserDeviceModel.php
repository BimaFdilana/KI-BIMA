<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDeviceModel extends Model
{
    protected $table = 'user_devices';

    protected $fillable = [
        'user_id',
        'device_id',
        'ip_address',
        'device_name',
        'user_agent',
        'fcm_token',
        'last_active_at',
        'expires_at',
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class);
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    /**
     * Update or create device with FCM token
     */
    public static function registerDevice(
        int $userId,
        string $deviceId,
        string $ipAddress,
        ?string $fcmToken = null,
        ?string $deviceName = null,
        ?string $userAgent = null
    ) {
        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'device_id' => $deviceId,
                'ip_address' => $ipAddress,
            ],
            [
                'fcm_token' => $fcmToken,
                'device_name' => $deviceName,
                'user_agent' => $userAgent,
                'last_active_at' => now(),
                'expires_at' => now()->addDays(30),
            ]
        );
    }
}
