<?php

namespace App\Listeners\Ticket;

use App\Events\Ticket\TicketCreated;
use App\Mail\Ticket\TicketCreated as TicketCreatedMail;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleTicketCreated implements ShouldQueue
{
    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * HandleTicketCreated constructor.
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param  TicketCreated  $event
     * @return void
     */
    public function handle(TicketCreated $event)
    {
        $ticket = $event->ticket;
        $users = $ticket->project->team()
            ->where('projects_teams.ticket_read', true)
            ->where(function ($q) {
                $q->where('settings->emails->ticket_create', true)->orWhere('settings', null);
            })->get();
        $this->mailer->to($users)
            ->send(new TicketCreatedMail($ticket));
    }
}
