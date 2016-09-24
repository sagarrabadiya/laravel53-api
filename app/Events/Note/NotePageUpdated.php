<?php

namespace App\Events\Note;

use App\Models\NotePage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NotePageUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var NotePage
     */
    public $page;

    /**
     * NotePageUpdated constructor.
     * @param NotePage $page
     */
    public function __construct(NotePage $page)
    {
        $this->page = $page;
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
