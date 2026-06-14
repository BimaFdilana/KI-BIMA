<?php

namespace App\Events\Information;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InformationDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $informationId;

    public function __construct(int $informationId)
    {
        $this->informationId = $informationId;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('informations');
    }

    public function broadcastAs(): string
    {
        return 'information.deleted';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->informationId,
        ];
    }
}
