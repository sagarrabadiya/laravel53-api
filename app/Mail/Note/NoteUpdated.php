<?php

namespace App\Mail\Note;

use App\Models\Note;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NoteUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $note;

    /**
     * NoteUpdated constructor.
     * @param Note $note
     */
    public function __construct(Note $note)
    {
        $this->note = $note;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.note.updated')
            ->subject('Milestone has been updated');
    }
}
