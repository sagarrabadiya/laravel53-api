<?php

namespace App\Listeners\Ticket;

use App\Events\Ticket\TicketStatusChanged;
use App\Mail\Ticket\TicketStatusChanged as TicketStatusChangedMail;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleTicketStatusChanged implements ShouldQueue
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
     * @param  TicketStatusChanged  $event
     * @return void
     */
    public function handle(TicketStatusChanged $event)
    {
        $ticket = $event->ticket;
        $users = $ticket->project->team()
            ->where('projects_teams.ticket_read', true)
            ->where(function ($q) {
                $q->where('settings->emails->ticket_status', true)->orWhere('settings', null);
            })->get();
        $this->mailer->to($users)
            ->send(new TicketStatusChangedMail($ticket));
    }
}
