<?php

namespace App\Listeners\Note;

use App\Events\Note\NotePageUpdated;
use App\Mail\Note\NotePageUpdated as NotePageUpdatedMail;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleNotePageUpdated implements ShouldQueue
{

    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * HandleNotePageUpdated constructor.
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param  NotePageUpdated  $event
     * @return void
     */
    public function handle(NotePageUpdated $event)
    {
        $note = $event->page->note;
        $users = $note->project->team()
            ->where('projects_teams.note_read', true)
            ->where(function ($q) {
                $q->where('settings->emails->note_page_update', true)->orWhere('settings', null);
            })->get();
        $this->mailer->to($users)
            ->send(new NotePageUpdatedMail($event->page));
    }
}
