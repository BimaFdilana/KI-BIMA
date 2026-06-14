<?php

namespace App\Listeners;

use App\Events\NotificationCreated;
use App\Services\Message\FirebaseCloudMessagingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

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
        try {
            $notification = $event->notification;

            // Check if push notifications are enabled for this notification type
            $settings = \App\Models\Auth\Notification\NotificationSettingModel::where([
                'user_type' => $notification->notifiable_type,
                'user_id' => $notification->notifiable_id,
                'notification_type' => $notification->type,
            ])->first();

            if ($settings && !$settings->is_push_enabled) {
                Log::info('Push notification disabled for user', [
                    'user_id' => $notification->notifiable_id,
                    'type' => $notification->type
                ]);
                return; // User has disabled push notifications for this type
            }

            // Extract notification data
            $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
            $title = $data['title'] ?? 'Kedai Indonesia';
            $body = $data['message'] ?? $data['body'] ?? 'You have a new notification';

            Log::info('Preparing to send FCM notification', [
                'notification_id' => $notification->id,
                'user_id' => $notification->notifiable_id,
                'type' => $notification->type,
                'title' => $title
            ]);

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

                Log::info('FCM notification sent successfully', [
                    'notification_id' => $notification->id,
                    'user_id' => $notification->notifiable_id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send FCM notification', [
                'notification_id' => $notification->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Don't throw exception to prevent job retry loop
            // You can choose to retry based on exception type
        }
    }
}
