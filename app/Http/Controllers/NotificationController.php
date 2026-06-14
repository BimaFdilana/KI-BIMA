<?php

namespace App\Http\Controllers;

use App\Services\Message\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth');
    }

    /**
     * Get user notifications.
     */
    public function index(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);

        $notifications = $this->notificationService->getUserNotifications(
            $request->user(),
            $limit,
            $offset
        );

        return response()->json([
            'data' => $notifications,
            'unread_count' => $this->notificationService->getUnreadCount($request->user()),
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = $this->notificationService->markAsRead($id);

        return response()->json([
            'message' => 'Notification marked as read',
            'notification' => $notification,
            'unread_count' => $this->notificationService->getUnreadCount($request->user()),
        ]);
    }

    /**
     * Mark notification as clicked.
     */
    public function markAsClicked(Request $request, string $id): JsonResponse
    {
        $notification = $this->notificationService->markAsClicked($id);

        return response()->json([
            'message' => 'Notification marked as clicked',
            'notification' => $notification,
        ]);
    }

    /**
     * Mark notification as downloaded.
     */
    public function markAsDownloaded(Request $request, string $id): JsonResponse
    {
        $notification = $this->notificationService->markAsDownloaded($id);

        return response()->json([
            'message' => 'Notification marked as downloaded',
            'notification' => $notification,
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->markAllNotificationsAsRead();

        return response()->json([
            'message' => 'All notifications marked as read',
            'unread_count' => 0,
        ]);
    }

    /**
     * Delete notification.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->notificationService->deleteNotification($id);

        return response()->json([
            'message' => 'Notification deleted',
        ]);
    }

    /**
     * Update notification settings.
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'notification_type' => 'required|string',
            'is_enabled' => 'boolean',
            'is_email_enabled' => 'boolean',
            'is_push_enabled' => 'boolean',
        ]);

        $settings = $request->user()->updateNotificationSetting(
            $validated['notification_type'],
            $request->only(['is_enabled', 'is_email_enabled', 'is_push_enabled'])
        );

        return response()->json([
            'message' => 'Notification settings updated',
            'settings' => $settings,
        ]);
    }

    /**
     * Subscribe to an entity.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subscribable_type' => 'required|string',
            'subscribable_id' => 'required|string',
        ]);

        $modelClass = $validated['subscribable_type'];
        $model = $modelClass::findOrFail($validated['subscribable_id']);

        $request->user()->subscribeTo($model);

        return response()->json([
            'message' => 'Subscribed successfully',
        ]);
    }

    /**
     * Unsubscribe from an entity.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subscribable_type' => 'required|string',
            'subscribable_id' => 'required|string',
        ]);

        $modelClass = $validated['subscribable_type'];
        $model = $modelClass::findOrFail($validated['subscribable_id']);

        $request->user()->unsubscribeFrom($model);

        return response()->json([
            'message' => 'Unsubscribed successfully',
        ]);
    }
}
