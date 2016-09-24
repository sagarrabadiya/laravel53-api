<?php

namespace App\Listeners\Note;

use App\Events\Note\NoteCreated;
use App\Mail\Note\NoteCreated as NoteCreatedMail;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleNoteCreated implements ShouldQueue
{
    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * HandleNoteCreated constructor.
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param  NoteCreated  $event
     * @return void
     */
    public function handle(NoteCreated $event)
    {
        $note = $event->note;
        $users = $note->project->team()
            ->where('projects_teams.note_read', true)
            ->where(function ($q) {
                $q->where('settings->emails->note_create', true)->orWhere('settings', null);
            })->get();
        $this->mailer->to($users)
            ->send(new NoteCreatedMail($note));
    }
}
