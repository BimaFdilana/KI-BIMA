<?php

namespace App\Livewire;

use App\Services\Message\NotificationService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class NotificationBadge extends Component
{
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

    public $unopenCount = 0;
    public $showDropdown = false;
    public $notifications = [];
    public $isLoading = true;
    public $page = 1;
    public $perPage = 4;
    public $hasMoreNotifications = true;
    public $isLoadingMore = false;
    public $activeTab = 'all';
    public $lastChecked; // Track last notification check time
    public $pollingEnabled = true; // Enable/disable polling

    protected $queryString = ['activeTab'];

    public function mount()
    {
        $this->loadUnopenCount();
        $this->lastChecked = now();
    }

    public function loadUnopenCount()
    {
        if (auth()->check()) {
            $this->unopenCount = auth()->user()->getNotificationCount('unopen');
        }
    }

    // Real-time polling method - check for new notifications
    public function checkForNewNotifications()
    {
        if (!auth()->check() || !$this->pollingEnabled) {
            return;
        }

        $user = auth()->user();
        $userRole = $user->roles->first()->name ?? 'guest';

        // Check if there are new notifications since last check
        $newNotificationsCount = DB::table('notifications')
            ->where(function ($query) use ($user, $userRole) {
                $query->where(function ($q) use ($user) {
                    $q->where('notifiable_type', 'App\Models\Auth\UserModel')
                        ->where('notifiable_id', $user->id);
                })->orWhere(function ($q) use ($userRole) {
                    $q->where('notifiable_type', 'App\Models\Role')
                        ->where('notifiable_id', $userRole);
                });
            })
            ->where('created_at', '>', $this->lastChecked)
            ->where('is_active', true)
            ->count();

        if ($newNotificationsCount > 0) {
            $this->lastChecked = now();
            $this->loadUnopenCount();

            // If dropdown is open, refresh notifications
            if ($this->showDropdown) {
                $this->page = 1;
                $this->loadNotifications();
            }

            // Dispatch browser notification
            $this->dispatch('new-notification-received', count: $newNotificationsCount);
        }
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;

        if ($this->showDropdown) {
            // Load notifications first
            $this->loadNotifications();

            // Update database status from 'unopen' to 'unread'
            $user = auth()->user();
            if ($user && $this->unopenCount > 0) {
                $userRole = $user->roles->first()->name ?? 'guest';

                $updated = DB::table('notifications')
                    ->where(function ($query) use ($user, $userRole) {
                        $query->where(function ($q) use ($user) {
                            $q->where('notifiable_type', 'App\Models\Auth\UserModel')
                                ->where('notifiable_id', $user->id);
                        })->orWhere(function ($q) use ($userRole) {
                            $q->where('notifiable_type', 'App\Models\Role')
                                ->where('notifiable_id', $userRole);
                        });
                    })
                    ->where('status', 'unopen')
                    ->where('is_active', true)  // Added this condition
                    ->update([
                        'status' => 'unread',
                        'updated_at' => now()->toDateTimeString()
                    ]);

                Log::info("Updated {$updated} notifications from unopen to unread");

                // Update local notifications array
                foreach ($this->notifications as $index => $notification) {
                    if ($notification['status'] === 'unopen') {
                        $this->notifications[$index]['status'] = 'unread';
                    }
                }
            }
            // Reset count
            $this->unopenCount = 0;
            $this->dispatch('all-notifications-open');
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->page = 1;
        $this->notifications = [];
        $this->hasMoreNotifications = true;
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        if (!auth()->check()) {
            return;
        }

        $this->isLoading = true;

        try {
            $user = auth()->user();
            $newNotifications = [];

            // Get the user's role for role-based notifications
            $userRole = $user->roles->first()->name ?? 'guest';

            // Query notifications based on the active tab
            switch ($this->activeTab) {
                case 'role':
                    // Get notifications specific to the user's role
                    $query = \DB::table('notifications')
                        ->where('notifiable_type', 'App\Models\Role')
                        ->where('notifiable_id', $userRole)
                        ->where('is_active', true)
                        ->orderBy('created_at', 'desc');
                    break;

                case 'personal':
                    // Get notifications specific to this user
                    $query = \DB::table('notifications')
                        ->where('notifiable_type', 'App\Models\Auth\UserModel')
                        ->where('notifiable_id', $user->id)
                        ->where('is_active', true)
                        ->orderBy('created_at', 'desc');
                    break;

                case 'all':
                default:
                    // Get all notifications for this user (personal + role-based)
                    $query = \DB::table('notifications')
                        ->where(function ($q) use ($user, $userRole) {
                            $q->where(function ($subq) use ($user) {
                                $subq->where('notifiable_type', 'App\Models\Auth\UserModel')
                                    ->where('notifiable_id', $user->id);
                            })->orWhere(function ($subq) use ($userRole) {
                                $subq->where('notifiable_type', 'App\Models\Role')
                                    ->where('notifiable_id', $userRole);
                            });
                        })
                        ->where('is_active', true)
                        ->orderBy('created_at', 'desc');
                    break;
            }

            // Count total records for pagination
            $count = $query->count();

            // Apply pagination
            if ($count > 0) {
                $newNotifications = $query->limit($this->perPage)
                    ->offset(($this->page - 1) * $this->perPage)
                    ->get()
                    ->map(function ($notification) {
                        // Ensure data is converted to array
                        $notification->data = json_decode($notification->data, true);
                        return (array)$notification;
                    })
                    ->toArray();

                $this->hasMoreNotifications = $count > ($this->page * $this->perPage);
            } else {
                $this->hasMoreNotifications = false;
            }

            // If this is the first load, replace notifications
            // If this is a load more, add to existing notifications
            if ($this->page === 1) {
                $this->notifications = $newNotifications;
            } else {
                $this->notifications = array_merge($this->notifications, $newNotifications);
            }
        } catch (\Exception $e) {
            Log::error('Error loading notifications: ' . $e->getMessage());
        } finally {
            // Always reset loading state
            $this->isLoading = false;
            $this->isLoadingMore = false;
        }
    }

    public function loadMore()
    {
        if ($this->isLoadingMore) {
            return;
        }

        $this->isLoadingMore = true;
        $this->page++;
        $this->loadNotifications();
    }

    public function closeDropdown()
    {
        $this->showDropdown = false;
    }

    public function markAsRead($id)
    {
        // Find the notification in local array first to check current status
        $currentNotification = collect($this->notifications)->firstWhere('id', $id);
        $wasUnopen = $currentNotification && ($currentNotification['status'] ?? '') === 'unopen';

        $service = app(NotificationService::class);
        if (method_exists($service, 'markAsRead')) {
            $service->markAsRead($id);
        } else {
            // Fallback if service method doesn't exist
            DB::table('notifications')
                ->where('id', $id)
                ->update([
                    'status' => 'read',
                    'read_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString()
                ]);
        }

        // Update local notification state
        foreach ($this->notifications as $index => $notification) {
            if ($notification['id'] == $id) {
                $this->notifications[$index]['status'] = 'read';
                $this->notifications[$index]['read_at'] = now()->toDateTimeString();
                break;
            }
        }

        // Only decrement unopen count if it was previously unopen
        if ($wasUnopen && $this->unopenCount > 0) {
            $this->unopenCount = max(0, $this->unopenCount - 1);
        }

        $this->dispatch('notification-read', id: $id);
    }

    public function handleNotificationDownload($id, $path = null)
    {
        // Find the notification in local array first
        $currentNotification = collect($this->notifications)->firstWhere('id', $id);
        $wasUnopen = $currentNotification && ($currentNotification['status'] ?? '') === 'unopen';

        // Mark notification as read first
        $this->markAsRead($id);

        // Set clicked_at timestamp and update status to 'clicked'
        DB::table('notifications')
            ->where('id', $id)
            ->update([
                'clicked_at' => now()->toDateTimeString(),
                'downloaded_at' => now()->toDateTimeString(),
                'status' => 'downloaded',
                'updated_at' => now()->toDateTimeString()
            ]);

        // Update in local state
        foreach ($this->notifications as $index => $notification) {
            if ($notification['id'] == $id) {
                $this->notifications[$index]['status'] = 'downloaded';
                $this->notifications[$index]['downloaded_at'] = now()->toDateTimeString();
                break;
            }
        }

        // Additional decrement for unopen count if needed (since clicking also marks as read)
        if ($wasUnopen && $this->unopenCount > 0) {
            $this->unopenCount = max(0, $this->unopenCount - 1);
        }

        // Close dropdown
        $this->showDropdown = false;

        // Redirect if path exists
        if ($path) {
            return redirect($path);
        }

        $this->dispatch('notification-clicked', id: $id);
    }

    public function handleNotificationClick($id, $path = null)
    {
        // Find the notification in local array first
        $currentNotification = collect($this->notifications)->firstWhere('id', $id);
        $wasUnopen = $currentNotification && ($currentNotification['status'] ?? '') === 'unopen';

        // Mark notification as read first
        $this->markAsRead($id);

        // Set clicked_at timestamp and update status to 'clicked'
        DB::table('notifications')
            ->where('id', $id)
            ->update([
                'clicked_at' => now()->toDateTimeString(),
                'status' => 'clicked',
                'updated_at' => now()->toDateTimeString()
            ]);

        // Update in local state
        foreach ($this->notifications as $index => $notification) {
            if ($notification['id'] == $id) {
                $this->notifications[$index]['status'] = 'clicked';
                $this->notifications[$index]['clicked_at'] = now()->toDateTimeString();
                break;
            }
        }

        // Additional decrement for unopen count if needed (since clicking also marks as read)
        if ($wasUnopen && $this->unopenCount > 0) {
            $this->unopenCount = max(0, $this->unopenCount - 1);
        }

        // Close dropdown
        $this->showDropdown = false;

        // Redirect if path exists
        if ($path) {
            return redirect($path);
        }

        $this->dispatch('notification-clicked', id: $id);
    }

    public function markAllAsRead()
    {
        $user = auth()->user();
        if (!$user) {
            return;
        }

        $userRole = $user->roles->first()->name ?? 'guest';

        if (method_exists($user, 'markAllNotificationsAsRead')) {
            $user->markAllNotificationsAsRead();
        } else {
            // Fallback if method doesn't exist
            DB::table('notifications')
                ->where(function ($query) use ($user, $userRole) {
                    $query->where(function ($q) use ($user) {
                        $q->where('notifiable_type', 'App\Models\Auth\UserModel')
                            ->where('notifiable_id', $user->id);
                    })->orWhere(function ($q) use ($userRole) {
                        $q->where('notifiable_type', 'App\Models\Role')
                            ->where('notifiable_id', $userRole);
                    });
                })
                ->whereIn('status', ['unopen', 'unread'])
                ->where('is_active', true)  // Added this condition
                ->update([
                    'status' => 'read',
                    'read_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString()
                ]);
        }

        // Update local state
        foreach ($this->notifications as $index => $notification) {
            if (in_array($this->notifications[$index]['status'] ?? '', ['unopen', 'unread'])) {
                $this->notifications[$index]['status'] = 'read';
                $this->notifications[$index]['read_at'] = now()->toDateTimeString();
            }
        }

        $this->unopenCount = 0;
        $this->dispatch('all-notifications-read');
    }

    // New method to handle download notifications
    public function handleDownload($id, $downloadUrl)
    {
        // Mark as downloaded
        DB::table('notifications')
            ->where('id', $id)
            ->update([
                'status' => 'downloaded',
                'downloaded_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString()
            ]);

        // Update in local state
        foreach ($this->notifications as $index => $notification) {
            if ($notification['id'] == $id) {
                $this->notifications[$index]['status'] = 'downloaded';
                $this->notifications[$index]['downloaded_at'] = now()->toDateTimeString();
                break;
            }
        }

        $this->dispatch('notification-downloaded', id: $id);
    }

    #[On('refresh-notifications')]
    #[On('new-notification')]
    #[On('notification-created')] // Additional event listener
    public function refreshNotifications()
    {
        $this->lastChecked = now();
        $this->loadUnopenCount();
        if ($this->showDropdown) {
            $this->page = 1; // Reset pagination
            $this->loadNotifications();
        }
    }

    // Method to enable/disable real-time polling
    public function togglePolling($enabled = true)
    {
        $this->pollingEnabled = $enabled;
    }

    public function viewAll()
    {
        $this->showDropdown = false;
        return redirect()->route('notifications.index');
    }

    public function render()
    {
        return view('livewire.notification-badge');
    }
}
