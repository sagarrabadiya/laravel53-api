<?php

namespace App\Mail\Note;

use App\Models\NotePage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotePageCreated extends Mailable
{
    use Queueable, SerializesModels;


    public $page;

    /**
     * NotePageCreated constructor.
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
        return $this->view('emails.note.page-created')
            ->subject('New Page is added in Notebook');
    }
}
