<?php

namespace App\Models\Auth\Notification;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationModel extends Model
{
    use HasUuids;

    protected $table = 'notifications';

    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'sender_type',
        'sender_id',
        'path',
        'status',
        'is_active',
        'is_system',
        'is_important'
    ];

    protected $casts = [
        'data' => 'array',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'is_important' => 'boolean',
        'read_at' => 'datetime',
        'clicked_at' => 'datetime',
        'downloaded_at' => 'datetime',
    ];

    /**
     * The notification's recipient.
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The notification's sender.
     */
    public function sender(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the notification groups.
     */
    public function groups(): HasMany
    {
        return $this->hasMany(NotificationGroupModel::class, 'notification_id');
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead()
    {
        $this->status = 'read';
        $this->read_at = now();
        $this->save();

        return $this;
    }

    /**
     * Mark the notification as clicked.
     */
    public function markAsClicked()
    {
        $this->status = 'clicked';
        $this->clicked_at = now();
        $this->save();

        return $this;
    }

    /**
     * Mark the notification as downloaded.
     */
    public function markAsDownloaded()
    {
        $this->status = 'downloaded';
        $this->downloaded_at = now();
        $this->save();

        return $this;
    }
}
