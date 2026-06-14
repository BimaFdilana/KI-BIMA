<?php

namespace App\Events;

use App\Models\Auth\Notification\NotificationModel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    /**
     * Create a new event instance.
     */
    public function __construct(NotificationModel $notification)
    {
        $this->notification = $notification;
    }
}
