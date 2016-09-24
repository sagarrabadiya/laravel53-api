<?php

namespace App\Events\Ticket;

use App\Models\Ticket;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TicketAssigneeChanged implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var Ticket
     */
    public $ticket;

    /**
     * TicketAssigneeChanged constructor.
     * @param Ticket $ticket
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
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
