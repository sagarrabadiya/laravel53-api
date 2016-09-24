<?php

namespace App\Events\BoardItem;

use App\Models\BoardItem;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BoardItemUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var BoardItem
     */
    public $boardItem;

    /**
     * Create a new event instance.
     * BoardItemUpdated constructor.
     * @param BoardItem $item
     */
    public function __construct(BoardItem $item)
    {
        $this->boardItem = $item;
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
