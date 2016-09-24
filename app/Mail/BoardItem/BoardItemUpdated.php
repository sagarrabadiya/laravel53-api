<?php

namespace App\Mail\BoardItem;

use App\Models\BoardItem;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BoardItemUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $boardItem;

    public $updater;

    public $project;

    /**
     * Create a new message instance.
     * BoardItemCreated constructor.
     * @param BoardItem $item
     */
    public function __construct(BoardItem $item)
    {
        $this->boardItem = $item;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.board_items.updated')
            ->subject('Board Item has been Updated');
    }
}
