<?php
// App/Events/Information/InformationUpdated.php

namespace App\Events\Information;

use App\Models\Information\Information;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InformationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $information;

    public function __construct(Information $information)
    {
        $this->information = $information;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('informations');
    }

    public function broadcastAs(): string
    {
        return 'information.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->information->id,
            'title' => $this->information->title,
            'description' => $this->information->description,
            'updated_at' => $this->information->updated_at,
        ];
    }
}
