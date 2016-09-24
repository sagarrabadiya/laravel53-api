<?php

namespace App\Listeners\Comment;

use App\Events\Comment\CommentCreated;
use App\Mail\Comment\CommentCreated as CommentCreatedMail;
use App\Models\Ticket;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleCommentCreated implements ShouldQueue
{
    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * HandleCommentCreated constructor.
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param  CommentCreated  $event
     * @return void
     */
    public function handle(CommentCreated $event)
    {
        $parent = $event->parentResource;
        $readKey = get_class($parent) == Ticket::class ? 'ticket_read' : 'board_read';
        $users = $parent->project->team()
            ->where('projects_teams.'.$readKey, true)
            ->where(function ($q) {
                $q->where('settings->emails->comment_create', true)->orWhere('settings', null);
            })->get();
        $this->mailer->to($users)
            ->send(new CommentCreatedMail($parent, $event->comment));
    }
}
