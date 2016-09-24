<?php

namespace App\Events\Comment;

use App\Models\Comment;
use App\Models\MainModel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CommentCreated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var MainModel
     */
    public $parentResource;

    /**
     * @var Comment
     */
    public $comment;

    /**
     * CommentCreated constructor.
     * @param MainModel $parent
     * @param Comment $comment
     */
    public function __construct(MainModel $parent, Comment $comment)
    {
        $this->parentResource = $parent;
        $this->comment = $comment;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
