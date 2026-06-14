<?php

namespace App\Services\Message;

use App\Models\Auth\Notification\NotificationModel;
use App\Models\Auth\Notification\NotificationGroupModel;
use App\Models\Auth\Notification\NotificationTemplateModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Parse dynamic variables in template string
     */
    public function parseTemplate(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            // Format angka menjadi format Rupiah (hanya pisahkan ribuan) jika key berkaitan dengan uang
            if (in_array($key, ['total', 'amount', 'limit']) && is_numeric($value)) {
                $value = number_format((float)$value, 0, ',', '.');
            }
            $template = str_replace('{' . $key . '}', (string) $value, $template);
        }
        return $template;
    }

    /**
     * Get active template by type
     */
    public function getTemplate(string $type): ?NotificationTemplateModel
    {
        return NotificationTemplateModel::where('type', $type)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Send order status change notification to a user
     * Updates existing notification if one exists for this transaction
     *
     * @param Model $user The user to notify
     * @param string $transactionId Order transaction ID
     * @param string $status New status
     * @param string $statusLabel Human-readable status label
     * @param float $total Order total amount
     * @param Model|null $sender The user who changed the status
     * @param string|null $path Path to view order details
     * @return NotificationModel
     */
    public function sendOrderStatusNotification(
        Model $user,
        string $transactionId,
        string $status,
        string $statusLabel,
        float $total,
        ?Model $sender = null,
        ?string $path = null
    ): NotificationModel {
        $data = [
            'transaction_id' => $transactionId,
            'status' => $status,
            'status_label' => $statusLabel,
            'total' => $total,
            'user_name' => $user->name ?? 'User',
            'updated_at' => now()->format('Y-m-d H:i:s')
        ];

        // Fetch dynamic template
        $template = $this->getTemplate('order_status_changed');
        if ($template) {
            $data['title'] = $this->parseTemplate($template->title_template, $data);
            $data['message'] = $this->parseTemplate($template->message_template, $data);
            if ($template->path_template) {
                $path = $this->parseTemplate($template->path_template, $data);
            }
        } else {
            // Fallback to hardcoded message
            $data['message'] = "Status pesanan #{$transactionId} telah berubah menjadi {$statusLabel}";
        }

        Log::info($data);

        // Find existing notification for this transaction
        $existingNotification = NotificationModel::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->where('type', 'order_status_changed')
            ->whereJsonContains('data->transaction_id', $transactionId)
            ->first();

        // Update existing notification or create new one
        if ($existingNotification) {
            $existingNotification->data = $data;
            $existingNotification->path = $path; // Update path if dynamic
            $existingNotification->status = 'unread'; 
            $existingNotification->updated_at = now();
            $existingNotification->save();

            event(new \App\Events\NotificationCreated($existingNotification));

            return $existingNotification;
        }

        // Create new notification if none exists
        return $this->sendToUser($user, 'order_status_changed', $data, $sender, $path);
    }

    public function refreshNotification(
        NotificationModel $notification,
        string $type,
        Model $notifiable,
        array $data,
        ?Model $sender = null,
        bool $isActive = true,
        bool $isSystem = false,
        bool $isImportant = false,
        ?string $path = null
    ): bool {
        try {
            $this->setSenderInfo($notification, $isSystem, $sender);

            $notification->fill([
                'type' => $type,
                'notifiable_type' => get_class($notifiable),
                'notifiable_id' => $notifiable->id,
                'data' => $data,
                'is_active' => $isActive,
                'is_important' => $isImportant,
                'path' => $path,
                'status' => 'unopen',
                'read_at' => null,
                'clicked_at' => null,
                'downloaded_at' => null,
                'updated_at' => now()
            ]);

            return $notification->save();
        } catch (\Throwable $th) {
            // Log error jika diperlukan
            \Log::error('Failed to refresh notification: ' . $th->getMessage(), [
                'notification_id' => $notification->id,
                'type' => $type,
                'notifiable_id' => $notifiable->id,
                'error' => $th->getMessage()
            ]);

            return false;
        }
    }

    private function setSenderInfo(NotificationModel $notification, bool $isSystem, ?Model $sender): void
    {
        if ($isSystem) {
            $notification->fill([
                'is_system' => true,
                'sender_id' => null,
                'sender_type' => 'System'
            ]);
        } else {
            $notification->fill([
                'is_system' => false,
                'sender_type' => $sender ? get_class($sender) : null,
                'sender_id' => $sender?->id
            ]);
        }
    }

    /**
     * Send order status change notification to store staff
     * Updates existing notification if one exists for this transaction
     *
     * @param string $role Role name (e.g., 'toko_supervisor')
     * @param string $transactionId Order transaction ID
     * @param string $status New status
     * @param string $statusLabel Human-readable status label
     * @param float $total Order total amount
     * @param string $tokoId Store ID
     * @param string $tokoName Store name
     * @param Model|null $sender The user who changed the status
     * @param string|null $path Path to view order details
     * @return NotificationModel
     */
    public function sendOrderStatusNotificationToRole(
        string $role,
        string $transactionId,
        string $status,
        string $statusLabel,
        float $total,
        string $tokoId,
        string $tokoName,
        ?Model $sender = null,
        ?string $path = null
    ): NotificationModel {
        $data = [
            'transaction_id' => $transactionId,
            'status' => $status,
            'status_label' => $statusLabel,
            'total' => $total,
            'toko_id' => $tokoId,
            'toko_name' => $tokoName,
            'updated_at' => now()->format('Y-m-d H:i:s')
        ];

        // Fetch dynamic template
        $template = $this->getTemplate('order_status_changed_role');
        if ($template) {
            $data['title'] = $this->parseTemplate($template->title_template, $data);
            $data['message'] = $this->parseTemplate($template->message_template, $data);
            if ($template->path_template) {
                $path = $this->parseTemplate($template->path_template, $data);
            }
        } else {
            // Fallback to hardcoded message
            $data['message'] = "Status pesanan #{$transactionId} di {$tokoName} telah berubah menjadi {$statusLabel}";
        }

        // Get the role
        $roleObj = Role::findByName($role);

        // Find existing notification for this transaction
        $existingNotification = NotificationModel::whereHas('groups', function ($query) use ($roleObj) {
            $query->where('group_type', 'role')
                ->where('group_id', $roleObj->id);
        })
            ->where('type', 'order_status_changed')
            ->whereJsonContains('data->transaction_id', $transactionId)
            ->first();

        // Update existing notification or create new one
        if ($existingNotification) {
            $existingNotification->data = $data;
            $existingNotification->path = $path; // Update path if dynamic
            $existingNotification->status = 'unread'; 
            $existingNotification->updated_at = now();
            $existingNotification->save();

            event(new \App\Events\NotificationCreated($existingNotification));

            return $existingNotification;
        }

        // Create new notification if none exists
        return $this->sendToRole($role, 'order_status_changed', $data, $sender, $path);
    }

    /**
     * Get status notification view data for rendering in UI
     *
     * @param array $notificationData The notification data
     * @return array Formatted data for UI
     */
    public function formatOrderStatusNotification(array $notificationData): array
    {
        $statusColors = [
            'pending' => 'yellow',
            'paid' => 'blue',
            'delivery' => 'purple',
            'success' => 'green',
            'failed' => 'red',
        ];

        $statusIcons = [
            'pending' => 'clock',
            'paid' => 'credit-card',
            'delivery' => 'truck',
            'success' => 'check-circle',
            'failed' => 'x-circle',
        ];

        return [
            'title' => $notificationData['title'] ?? 'Kedai Indonesia',
            'message' => $notificationData['message'],
            'transaction_id' => $notificationData['transaction_id'],
            'status' => $notificationData['status'],
            'status_label' => $notificationData['status_label'],
            'total' => number_format($notificationData['total'], 0, ',', '.'),
            'updated_at' => $notificationData['updated_at'],
            'color' => $statusColors[$notificationData['status']] ?? 'gray',
            'icon' => $statusIcons[$notificationData['status']] ?? 'bell',
            'path' => $notificationData['path'] ?? null,
        ];
    }

    // Keep all other existing methods from the original NotificationService class
    public function sendToUser(Model $user, string $type, array $data, ?Model $sender = null, ?string $path = null): NotificationModel
    {
        return $this->createNotification([
            'type' => $type,
            'data' => $data,
            'sender_type' => $sender ? get_class($sender) : null,
            'sender_id' => $sender ? $sender->id : null,
            'path' => $path,
            'notifiable_type' => get_class($user),
            'notifiable_id' => $user->id,
        ]);
    }

    public function sendToUserFromSystem($user, string $type, array $data, ?string $path = null): NotificationModel
    {
        return $this->createNotification([
            'type' => $type,
            'notifiable_type' => get_class($user),
            'notifiable_id' => $user->id,
            'data' => $data,
            'sender_type' => null,
            'sender_id' => null,
            'path' => $path,
            'status' => 'unopen',
            'is_active' => true,
            'is_system' => true,
            'is_important' => false
        ]);
    }

    /**
     * Send notification to multiple users.
     */
    public function sendToUsers(Collection $users, string $type, array $data, ?Model $sender = null, ?string $path = null): Collection
    {
        $notifications = collect();
        foreach ($users as $user) {
            if ($user->isNotificationEnabled($type)) {
                $notifications->push($this->sendToUser($user, $type, $data, $sender, $path));
            }
        }
        $this->broadcastNewNotification($notifications->first()->id);
        return $notifications;
    }

    /**
     * Send notification to users with specific role.
     */
    public function sendToRole(string $roleName, string $type, array $data, ?Model $sender = null, ?string $path = null): NotificationModel
    {
        $role = Role::findByName($roleName);
        $notification = $this->createNotification([
            'type' => $type,
            'data' => $data,
            'sender_type' => $sender ? get_class($sender) : null,
            'sender_id' => $sender ? $sender->id : null,
            'path' => $path,
            'is_system' => true,
        ]);
        // Create notification group
        NotificationGroupModel::create([
            'notification_id' => $notification->id,
            'group_type' => 'role',
            'group_id' => $role->id,
        ]);
        $this->broadcastNewNotification($notification->id);
        return $notification;
    }

    /**
     * Send notification to all subscribers of an entity.
     */
    public function sendToSubscribers(Model $subscribable, string $type, array $data, ?Model $sender = null, ?string $path = null): Collection
    {
        $subscriptions = DB::table('notification_subscriptions')
            ->where('subscribable_type', get_class($subscribable))
            ->where('subscribable_id', $subscribable->id)
            ->where('is_active', true)
            ->get();
        $notifications = collect();
        foreach ($subscriptions as $subscription) {
            $userClass = $subscription->user_type;
            $user = $userClass::find($subscription->user_id);
            if ($user && $user->isNotificationEnabled($type)) {
                $notifications->push($this->sendToUser($user, $type, $data, $sender, $path));
            }
        }
        $this->broadcastNewNotification($notifications->first()->id);
        return $notifications;
    }

    /**
     * Send system notification to all users.
     */
    public function sendSystemNotification(string $type, array $data, ?string $path = null): NotificationModel
    {
        return $this->createNotification([
            'type' => $type,
            'data' => $data,
            'path' => $path,
            'is_system' => true,
        ]);
        $this->broadcastNewNotification($notification->id);
        return $notification;
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(string $id): NotificationModel
    {
        $notification = NotificationModel::findOrFail($id);
        return $notification->markAsRead();
    }

    public function markAllAsReadForUser($userId)
    {
        DB::table('notifications')
            ->where('notifiable_type', 'App\Models\Auth\UserModel')
            ->where('notifiable_id', $userId)
            ->whereIn('status', ['unopen', 'unread'])
            ->update([
                'status' => 'read',
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        // Broadcast update to components
        $this->broadcastNotificationRefresh();
    }
    /**
     * Mark notification as clicked.
     */
    public function markAsClicked(string $id): NotificationModel
    {
        $notification = NotificationModel::findOrFail($id);
        return $notification->markAsClicked();
    }

    /**
     * Mark notification as downloaded.
     */
    public function markAsDownloaded(string $id): NotificationModel
    {
        $notification = NotificationModel::findOrFail($id);
        return $notification->markAsDownloaded();
    }

    /**
     * Delete notification (soft delete).
     */
    public function deleteNotification(string $id): bool
    {
        $notification = NotificationModel::findOrFail($id);
        return $notification->delete();
    }

    /**
     * Get notifications for a user.
     */
    public function getUserNotifications(Model $user, int $limit = 10, int $offset = 0): Collection
    {
        // Get direct notifications
        $directNotifications = $user->notifications()
            ->offset($offset)
            ->limit($limit)
            ->get();

        \Log::info($directNotifications);
        // Get group notifications (based on roles)
        $userRoleIds = $user->roles()->pluck('id')->toArray();
        if (!empty($userRoleIds)) {
            $groupNotifications = NotificationModel::whereHas('groups', function ($query) use ($userRoleIds) {
                $query->where('group_type', 'role')
                    ->whereIn('group_id', $userRoleIds);
            })
                ->where('is_active', true)
                ->offset($offset)
                ->limit($limit)
                ->get();
            // Merge and sort notifications
            return $directNotifications->merge($groupNotifications)
                ->sortByDesc('created_at')
                ->take($limit);
        }
        return $directNotifications;
    }

    /**
     * Get unread notifications count for a user.
     */
    public function getUnreadCount(Model $user): int
    {
        // Count direct unread notifications
        $directCount = $user->unreadNotifications()->count();
        // Count group unread notifications
        $userRoleIds = $user->roles()->pluck('id')->toArray();
        if (!empty($userRoleIds)) {
            $groupCount = NotificationModel::whereHas('groups', function ($query) use ($userRoleIds) {
                $query->where('group_type', 'role')
                    ->whereIn('group_id', $userRoleIds);
            })
                ->where('is_active', true)
                ->where('status', 'unread')
                ->count();
            return $directCount + $groupCount;
        }
        return $directCount;
    }

    /**
     * Create a notification.
     */
    protected function createNotification(array $data): NotificationModel
    {
        $notification = NotificationModel::create([
            'type' => $data['type'] ?? 'general',
            'notifiable_type' => $data['notifiable_type'] ?? null,
            'notifiable_id' => $data['notifiable_id'] ?? null,
            'data' => $data['data'] ?? [],
            'sender_type' => $data['sender_type'] ?? null,
            'sender_id' => $data['sender_id'] ?? null,
            'path' => $data['path'] ?? null,
            'status' => $data['status'] ?? 'unopen',
            'is_active' => $data['is_active'] ?? true,
            'is_system' => $data['is_system'] ?? false,
            'is_important' => $data['is_important'] ?? false,
        ]);

        // Dispatch event for push notification
        event(new \App\Events\NotificationCreated($notification));

        return $notification;
    }

    /**
     * Broadcast new notification event (optional - for real-time updates).
     */
    protected function broadcastNewNotification($notificationId): void
    {
        // Optional: Broadcast to real-time channel if you have Pusher/WebSockets set up
        // event(new NewNotificationEvent($notificationId));
    }

    /**
     * Broadcast notification refresh event (optional - for real-time updates).
     */
    protected function broadcastNotificationRefresh(): void
    {
        // Optional: Broadcast refresh event if you have Pusher/WebSockets set up
        // event(new NotificationRefreshEvent());
    }
}

