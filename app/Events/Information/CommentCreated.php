<?php
// App/Events/Information/CommentCreated.php

namespace App\Events\Information;

use App\Models\Information\InformationComment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;
    public $informationId;

    public function __construct(InformationComment $comment, int $informationId)
    {
        $this->comment = $comment;
        $this->informationId = $informationId;
    }

    public function broadcastOn(): Channel
    {
        return new Channel("information.{$this->informationId}");
    }

    public function broadcastAs(): string
    {
        return 'comment.created';
    }

    public function broadcastWith(): array
    {
        $this->comment->load('user');

        return [
            'comment' => (new \App\Http\Resources\InformationCommentResource($this->comment))->resolve()
        ];
    }
}
