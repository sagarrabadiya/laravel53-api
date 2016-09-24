<?php

namespace App\Listeners\Ticket;

use App\Events\Ticket\TicketUpdated;
use App\Mail\Ticket\TicketUpdated as TicketUpdatedMail;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleTicketUpdated implements ShouldQueue
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
     * @param  TicketUpdated  $event
     * @return void
     */
    public function handle(TicketUpdated $event)
    {
        $ticket = $event->ticket;
        $users = $ticket->project->team()
            ->where('projects_teams.ticket_read', true)
            ->where(function ($q) {
                $q->where('settings->emails->ticket_update', true)->orWhere('settings', null);
            })->get();
        $this->mailer->to($users)
            ->send(new TicketUpdatedMail($ticket));
    }
}
