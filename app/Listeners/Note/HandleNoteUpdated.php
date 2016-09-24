<?php

namespace App\Listeners\Note;

use App\Events\Note\NoteUpdated;
use App\Mail\Note\NoteUpdated as NoteUpdatedMail;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleNoteUpdated implements ShouldQueue
{

    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * HandleNoteUpdated constructor.
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param  NoteUpdated  $event
     * @return void
     */
    public function handle(NoteUpdated $event)
    {
        $note = $event->note;
        $users = $note->project->team()
            ->where('projects_teams.note_read', true)
            ->where(function ($q) {
                $q->where('settings->emails->note_update', true)->orWhere('settings', null);
            })->get();
        $this->mailer->to($users)
            ->send(new NoteUpdatedMail($note));
    }
}
