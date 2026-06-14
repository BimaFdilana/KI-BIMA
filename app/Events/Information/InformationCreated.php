<?php

namespace App\Events\Information;

use App\Models\Information\Information;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InformationCreated implements ShouldBroadcast
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
        return 'information.created';
    }

    public function broadcastWith(): array
    {
        $this->information->load(['user', 'category', 'media']);

        return [
            'information' => (new \App\Http\Resources\InformationResource($this->information))->resolve()
        ];
    }
}
