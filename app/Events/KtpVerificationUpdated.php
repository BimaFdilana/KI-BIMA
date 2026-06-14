<?php

namespace App\Events;

use App\Models\Auth\UserModel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KtpVerificationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $action;
    public int $userId;
    public string $userName;

    /**
     * Create a new event instance.
     */
    public function __construct(UserModel $user, string $action)
    {
        $this->action = $action; // 'verified' or 'rejected'
        $this->userId = $user->id;
        $this->userName = $user->name;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('approval-dashboard');
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'ktp.verification.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'type' => 'ktp',
            'action' => $this->action,
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
