<?php

namespace App\Mail\Ticket;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketAssigneeChanged extends Mailable
{
    use Queueable, SerializesModels;

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
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.tickets.assigned_to')
            ->subject(
                'Ticket no. '. $this->ticket->sequence_id." has been assigned to ". $this->ticket->assignedTo->name
            );
    }
}
