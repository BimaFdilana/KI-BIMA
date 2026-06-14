<?php

namespace App\Listeners;

use App\Events\NotificationCreated;
use App\Services\FirebaseCloudMessagingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendFcmNotification implements ShouldQueue
{
    use InteractsWithQueue;
    protected $fcmService;
    public function __construct(FirebaseCloudMessagingService $fcmService)
    {
        $this->fcmService = $fcmService;
    }
    public function handle(NotificationCreated $event)
    {
        $notification = $event->notification;
        // Check if push notifications are enabled for this notification type
        $settings = \App\Models\Auth\Notification\NotificationSettingModel::where([
            'user_type' => $notification->notifiable_type,
            'user_id' => $notification->notifiable_id,
            'notification_type' => $notification->type,
        ])->first();
        if ($settings && !$settings->is_push_enabled) {
            return; // User has disabled push notifications for this type
        }
        // Extract notification data
        $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
        $title = $data['title'] ?? 'Kedai Indonesia';
        $body = $data['message'] ?? $data['body'] ?? 'You have a new notification';
        // Send to user
        if ($notification->notifiable_type === 'App\\Models\\Auth\\UserModel') {
            $this->fcmService->sendToUser(
                $notification->notifiable_id,
                $title,
                $body,
                [
                    'notification_id' => $notification->id,
                    'type' => $notification->type,
                    'path' => $notification->path ?? '',
                ]
            );
        }
    }
}
