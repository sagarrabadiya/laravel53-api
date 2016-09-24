<?php

namespace App\Mail\Note;

use App\Models\NotePage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotePageUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $page;

    /**
     * NotePageUpdated constructor.
     * @param NotePage $page
     */
    public function __construct(NotePage $page)
    {
        $this->page = $page;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.note.page-updated')
            ->subject('Page is updated in Notebook');
    }
}
