<?php

namespace App\Events\Milestone;

use App\Models\Milestone;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MilestoneUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var Milestone
     */
    public $milestone;

    /**
     * MilestoneCreated constructor.
     * @param Milestone $milestone
     */
    public function __construct(Milestone $milestone)
    {
        $this->milestone = $milestone;
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
