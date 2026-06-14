<?php

namespace App\Traits;

use App\Models\Auth\Notification\NotificationModel;
use App\Models\Auth\Notification\NotificationSettingsModel;
use App\Models\Auth\Notification\NotificationSubscriptionModel;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasNotifications
{
    /**
     * Get all notifications for this user.
     */
    public function customNotifications()
    {
        return NotificationModel::where('notifiable_id', $this->id)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get unread notifications for this user.
     */
    public function unreadCustomNotifications()
    {
        return $this->customNotifications()->where('status', 'unread');
    }

    /**
     * Get notification settings for this user.
     */
    public function notificationSettings(): MorphMany
    {
        return $this->morphMany(NotificationSettingsModel::class, 'user');
    }

    /**
     * Get notification subscriptions for this user.
     */
    public function notificationSubscriptions(): MorphMany
    {
        return $this->morphMany(NotificationSubscriptionModel::class, 'user')
            ->where('is_active', true);
    }

    /**
     * Get notification count based on status.
     */
    public function getNotificationCount(?string $status = null): int
    {
        $query = $this->customNotifications();

        if ($status === 'unread') {
            $query->where('status', 'unread');
        } elseif ($status === 'read') {
            $query->where('status', 'read');
        }

        return $query->count();
    }

    /**
     * Get paginated notifications.
     */
    public function getPaginatedNotifications($perPage = 10, $offset = 0)
    {
        return $this->customNotifications()
            ->skip($offset)
            ->take($perPage)
            ->get();
    }

    /**
     * Send a notification to this user.
     */
    public function sendNotification(array $data): NotificationModel
    {
        return NotificationModel::create([
            'notifiable_id' => $this->id,
            'notifiable_type' => get_class($this),
            'is_active' => true,
            'status' => 'unread',
            ...$data
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsAsRead(): void
    {
        $this->customNotifications()
            ->where('status', 'unread')
            ->update([
                'status' => 'read',
                'read_at' => now(),
            ]);
    }

    /**
     * Subscribe to an entity.
     */
    public function subscribeTo($subscribable): NotificationSubscriptionModel
    {
        return $this->notificationSubscriptions()->updateOrCreate(
            [
                'subscribable_type' => get_class($subscribable),
                'subscribable_id' => $subscribable->id,
            ],
            ['is_active' => true]
        );
    }

    /**
     * Unsubscribe from an entity.
     */
    public function unsubscribeFrom($subscribable): void
    {
        $this->notificationSubscriptions()
            ->where('subscribable_type', get_class($subscribable))
            ->where('subscribable_id', $subscribable->id)
            ->update(['is_active' => false]);
    }

    /**
     * Check if user is subscribed to an entity.
     */
    public function isSubscribedTo($subscribable): bool
    {
        return $this->notificationSubscriptions()
            ->where('subscribable_type', get_class($subscribable))
            ->where('subscribable_id', $subscribable->id)
            ->exists();
    }

    /**
     * Get notification setting for specific type.
     */
    public function getNotificationSetting(string $type): NotificationSettingsModel
    {
        return $this->notificationSettings()->firstOrCreate(
            ['notification_type' => $type],
            [
                'is_enabled' => true,
                'is_email_enabled' => false,
                'is_push_enabled' => false,
            ]
        );
    }

    /**
     * Update notification setting.
     */
    public function updateNotificationSetting(string $type, array $settings): NotificationSettingsModel
    {
        $setting = $this->getNotificationSetting($type);
        $setting->update($settings);
        return $setting;
    }

    /**
     * Check if notification type is enabled.
     */
    public function isNotificationEnabled(string $type): bool
    {
        return $this->getNotificationSetting($type)->is_enabled;
    }
}
