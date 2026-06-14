<?php

namespace App\Events\Information;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $commentId;
    public $informationId;

    public function __construct(int $commentId, int $informationId)
    {
        $this->commentId = $commentId;
        $this->informationId = $informationId;
    }

    public function broadcastOn(): Channel
    {
        return new Channel("information.{$this->informationId}");
    }

    public function broadcastAs(): string
    {
        return 'comment.deleted';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->commentId,
            'information_id' => $this->informationId,
        ];
    }
}
