<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationAPI extends Controller
{
    /**
     * Get user's notifications with pagination
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function notificationView(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $type = $request->input('type', null);
        $status = $request->input('status', null);
        $isImportant = $request->input('is_important', null);

        // Get notifications for the authenticated user
        $query = DB::table('notifications')
            ->where(function ($query) use ($user) {
                // Get direct notifications for this user
                $query->where('notifiable_type', 'App\Models\Auth\UserModel')
                    ->where('notifiable_id', $user->id);

                // Or get notifications for user's roles
                $query->orWhere(function ($q) use ($user) {
                    $q->where('notifiable_type', 'App\Models\Role');

                    // Get user's role names
                    $userRoles = $user->roles->pluck('name')->toArray();

                    $q->whereIn('notifiable_id', $userRoles);
                });
            })
            ->where('is_active', true);

        // Apply filters if provided
        if ($type) {
            $query->where('type', $type);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($isImportant !== null) {
            $query->where('is_important', $isImportant);
        }

        // Get total count for pagination
        $total = $query->count();

        // Get paginated results
        $notifications = $query->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Format the response
        $formattedNotifications = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'data' => json_decode($notification->data),
                'path' => $notification->path,
                'status' => $notification->status,
                'is_important' => (bool)$notification->is_important,
                'is_system' => (bool)$notification->is_system,
                'created_at' => Carbon::parse($notification->created_at)->format('Y-m-d H:i:s'),
                'read_at' => $notification->read_at ? Carbon::parse($notification->read_at)->format('Y-m-d H:i:s') : null,
            ];
        });

        return response()->json([
            'data' => $formattedNotifications,
            'meta' => [
                'current_page' => (int)$page,
                'per_page' => (int)$perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
            ]
        ]);
    }

    /**
     * Get unread notifications count
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnreadCount()
    {
        $user = Auth::user();

        // Count unread notifications
        $count = DB::table('notifications')
            ->where(function ($query) use ($user) {
                // Direct notifications for this user
                $query->where('notifiable_type', 'App\Models\Auth\UserModel')
                    ->where('notifiable_id', $user->id);

                // Or notifications for user's roles
                $query->orWhere(function ($q) use ($user) {
                    $q->where('notifiable_type', 'App\Models\Role');

                    // Get user's role names
                    $userRoles = $user->roles->pluck('name')->toArray();

                    $q->whereIn('notifiable_id', $userRoles);
                });
            })
            ->where('is_active', true)
            ->where('status', 'unopen')
            ->count();

        return response()->json([
            'unread_count' => $count
        ]);
    }

    /**
     * Mark notification as read
     *
     * @param string $id Notification ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        $user = Auth::user();

        // Check if notification exists and is relevant to user
        $notification = DB::table('notifications')
            ->where('id', $id)
            ->where(function ($query) use ($user) {
                // Direct notifications for this user
                $query->where('notifiable_type', 'App\Models\Auth\UserModel')
                    ->where('notifiable_id', $user->id);

                // Or notifications for user's roles
                $query->orWhere(function ($q) use ($user) {
                    $q->where('notifiable_type', 'App\Models\Role');

                    // Get user's role names
                    $userRoles = $user->roles->pluck('name')->toArray();

                    $q->whereIn('notifiable_id', $userRoles);
                });
            })
            ->first();

        if (!$notification) {
            return response()->json([
                'message' => 'Pemberitahuan tidak ditemukan'
            ], 404);
        }

        // Update the notification status
        DB::table('notifications')
            ->where('id', $id)
            ->update([
                'status' => 'read',
                'read_at' => now(),
                'updated_at' => now()
            ]);

        return response()->json([
            'message' => 'Pemberitahuan berhasil di mark as read'
        ]);
    }

    /**
     * Mark notification as clicked
     *
     * @param string $id Notification ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsClicked($id)
    {
        $user = Auth::user();

        // Check if notification exists and is relevant to user
        $notification = DB::table('notifications')
            ->where('id', $id)
            ->where(function ($query) use ($user) {
                // Direct notifications for this user
                $query->where('notifiable_type', 'App\Models\Auth\UserModel')
                    ->where('notifiable_id', $user->id);

                // Or notifications for user's roles
                $query->orWhere(function ($q) use ($user) {
                    $q->where('notifiable_type', 'App\Models\Role');

                    // Get user's role names
                    $userRoles = $user->roles->pluck('name')->toArray();

                    $q->whereIn('notifiable_id', $userRoles);
                });
            })
            ->first();

        if (!$notification) {
            return response()->json([
                'message' => 'Pemberitahuan tidak ditemukan'
            ], 404);
        }

        // Update the notification status
        DB::table('notifications')
            ->where('id', $id)
            ->update([
                'status' => 'clicked',
                'read_at' => now(),
                'updated_at' => now()
            ]);

        return response()->json([
            'message' => 'Pemberitahuan berhasil di mark as clicked'
        ]);
    }

    /**
     * Mark all notifications as read
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        $user = Auth::user();

        // Update all unread notifications for this user
        $updated = DB::table('notifications')
            ->where(function ($query) use ($user) {
                // Direct notifications for this user
                $query->where('notifiable_type', 'App\Models\Auth\UserModel')
                    ->where('notifiable_id', $user->id);

                // Or notifications for user's roles
                $query->orWhere(function ($q) use ($user) {
                    $q->where('notifiable_type', 'App\Models\Role');

                    // Get user's role names
                    $userRoles = $user->roles->pluck('name')->toArray();

                    $q->whereIn('notifiable_id', $userRoles);
                });
            })
            ->where('is_active', true)
            ->whereNull('read_at')
            ->update([
                'status' => 'read',
                'read_at' => now(),
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => "Berhasil menandai $updated notifikasi sebagai dibaca"
        ]);
    }

    /**
     * Update notification settings
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'notification_type' => 'required|string',
            'is_enabled' => 'boolean',
            'is_email_enabled' => 'boolean',
            'is_push_enabled' => 'boolean',
        ]);

        // Find existing setting or create new one
        $setting = DB::table('notification_settings')
            ->where('user_type', 'App\Models\Auth\UserModel')
            ->where('user_id', $user->id)
            ->where('notification_type', $request->notification_type)
            ->first();

        $data = [
            'updated_at' => now()
        ];

        if ($request->has('is_enabled')) {
            $data['is_enabled'] = $request->is_enabled;
        }

        if ($request->has('is_email_enabled')) {
            $data['is_email_enabled'] = $request->is_email_enabled;
        }

        if ($request->has('is_push_enabled')) {
            $data['is_push_enabled'] = $request->is_push_enabled;
        }

        if ($setting) {
            // Update existing setting
            DB::table('notification_settings')
                ->where('user_type', 'App\Models\Auth\UserModel')
                ->where('user_id', $user->id)
                ->where('notification_type', $request->notification_type)
                ->update($data);
        } else {
            // Create new setting
            DB::table('notification_settings')->insert([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'user_type' => 'App\Models\Auth\UserModel',
                'user_id' => $user->id,
                'notification_type' => $request->notification_type,
                'is_enabled' => $request->input('is_enabled', true),
                'is_email_enabled' => $request->input('is_email_enabled', false),
                'is_push_enabled' => $request->input('is_push_enabled', false),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Pengaturan pemberitahuan berhasil diupdate'
        ]);
    }

    /**
     * Get user notification settings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSettings()
    {
        $user = Auth::user();

        $settings = DB::table('notification_settings')
            ->where('user_type', 'App\Models\Auth\UserModel')
            ->where('user_id', $user->id)
            ->get();

        return response()->json([
            'data' => $settings
        ]);
    }

    /**
     * Send test notification to current user
     * For debugging Firebase connection
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendTestNotification()
    {
        $user = Auth::user();

        try {
            // Use NotificationService to send test notification
            $notificationService = app(\App\Services\Message\NotificationService::class);

            $notificationData = [
                'message' => '🧪 Test Notification from Backend',
                'details' => 'This is a test notification sent at ' . now()->format('Y-m-d H:i:s'),
                'test' => 'true',
            ];

            // Send to user
            $notificationService->sendToUser(
                user: $user,
                type: 'test_notification',
                data: $notificationData
            );

            return response()->json([
                'success' => true,
                'message' => '✅ Test notification sent successfully!',
                'details' => [
                    'user_id' => $user->id,
                    'user' => $user->username,
                    'timestamp' => now()->toIso8601String(),
                    'note' => 'Check your device notification panel. May take 1-5 seconds to appear.',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Failed to send test notification',
                'error' => $e->getMessage(),
                'troubleshooting' => [
                    '1. Ensure user has registered FCM tokens (check user_devices table)',
                    '2. Check if queue worker is running: php artisan queue:work',
                    '3. Verify Firebase credentials file exists',
                    '4. Check Laravel logs: storage/logs/laravel.log',
                ]
            ], 500);
        }
    }
}
