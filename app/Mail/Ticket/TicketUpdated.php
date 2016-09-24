<?php

namespace App\Mail\Ticket;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketUpdated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Ticket
     */
    public $ticket;

    /**
     * TicketUpdated constructor.
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
        return $this->view('emails.tickets.updated')
            ->subject('Ticket no. '. $this->ticket->sequence_id." has been updated.");
    }
}
