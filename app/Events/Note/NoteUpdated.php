<?php

namespace App\Events\Note;

use App\Models\Note;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NoteUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;


    /**
     * @var Note
     */
    public $note;

    /**
     * NoteUpdated constructor.
     * @param Note $note
     */
    public function __construct(Note $note)
    {
        $this->note = $note;
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
