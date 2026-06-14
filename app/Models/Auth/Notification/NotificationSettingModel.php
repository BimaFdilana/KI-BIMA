<?php

namespace App\Models\Auth\Notification;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NotificationSettingModel extends Model
{
    use HasUuids;

    protected $table = 'notification_settings';

    protected $fillable = [
        'user_type',
        'user_id',
        'notification_type',
        'is_enabled',
        'is_email_enabled',
        'is_push_enabled'
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_email_enabled' => 'boolean',
        'is_push_enabled' => 'boolean',
    ];

    /**
     * Get the user that owns the settings.
     */
    public function user(): MorphTo
    {
        return $this->morphTo();
    }
}
