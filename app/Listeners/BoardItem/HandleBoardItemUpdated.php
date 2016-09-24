<?php

namespace App\Listeners\BoardItem;

use App\Events\BoardItem\BoardItemUpdated;
use App\Mail\BoardItem\BoardItemUpdated as BoardItemUpdatedMail;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleBoardItemUpdated implements ShouldQueue
{

    protected $mailer;

    /**
     * Create the event listener.
     * HandleBoardItemUpdated constructor.
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param  BoardItemUpdated  $event
     * @return void
     */
    public function handle(BoardItemUpdated $event)
    {
        $boardItem = $event->boardItem;
        $users = $boardItem->project->team()
            ->where('projects_teams.board_read', true)
            ->where(function ($q) {
                $q->where('settings->emails->board_update', true)->orWhere('settings', null);
            })->get();
        $this->mailer->to($users)
            ->send(new BoardItemUpdatedMail($event->boardItem));
    }
}
