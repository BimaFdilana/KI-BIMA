<?php

namespace App\Events;

use App\Models\Toko\TokoModel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TokoVerificationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $action;
    public int $tokoId;
    public string $tokoName;

    /**
     * Create a new event instance.
     */
    public function __construct(TokoModel $toko, string $action)
    {
        $this->action = $action; // 'approved' or 'rejected'
        $this->tokoId = $toko->id;
        $this->tokoName = $toko->name;
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
        return 'toko.verification.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'type' => 'toko',
            'action' => $this->action,
            'toko_id' => $this->tokoId,
            'toko_name' => $this->tokoName,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
