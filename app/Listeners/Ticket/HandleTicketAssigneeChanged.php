<?php

namespace App\Listeners\Ticket;

use App\Events\Ticket\TicketAssigneeChanged;
use App\Mail\Ticket\TicketAssigneeChanged as TicketAssigneeChangedMail;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleTicketAssigneeChanged implements ShouldQueue
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
     * @param  TicketAssigneeChanged  $event
     * @return void
     */
    public function handle(TicketAssigneeChanged $event)
    {
        $ticket = $event->ticket;
        $users = $ticket->project->team()
            ->where('projects_teams.ticket_read', true)
            ->where(function ($q) {
                $q->where('settings->emails->ticket_assigned', true)->orWhere('settings', null);
            })->get();
        $this->mailer->to($users)
            ->send(new TicketAssigneeChangedMail($ticket));
    }
}
