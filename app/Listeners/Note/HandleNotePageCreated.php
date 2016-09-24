<?php

namespace App\Listeners\Note;

use App\Events\Note\NotePageCreated;
use App\Mail\Note\NotePageCreated as NotePageCreatedMail;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleNotePageCreated implements ShouldQueue
{
    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * HandleNotePageCreated constructor.
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param  NotePageCreated  $event
     * @return void
     */
    public function handle(NotePageCreated $event)
    {
        $note = $event->page->note;
        $users = $note->project->team()
            ->where('projects_teams.note_read', true)
            ->where(function ($q) {
                $q->where('settings->emails->note_page_create', true)->orWhere('settings', null);
            })->get();
        $this->mailer->to($users)
            ->send(new NotePageCreatedMail($event->page));
    }
}
