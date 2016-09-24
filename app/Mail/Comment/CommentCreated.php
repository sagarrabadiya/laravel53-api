<?php

namespace App\Mail\Comment;

use App\Models\Comment;
use App\Models\MainModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CommentCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var MainModel
     */
    public $parentResource;

    /**
     * @var Comment
     */
    public $comment;

    /**
     * CommentCreated constructor.
     * @param MainModel $parent
     * @param Comment $comment
     */
    public function __construct(MainModel $parent, Comment $comment)
    {
        $this->parentResource = $parent;
        $this->comment = $comment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.comments.created');
    }
}
