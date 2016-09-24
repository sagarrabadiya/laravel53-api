<?php

namespace App\Listeners\BoardItem;

use App\Events\BoardItem\BoardItemCreated;
use App\Mail\BoardItem\BoardItemCreated as BoardItemCreatedMail;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleBoardItemCreated implements ShouldQueue
{

    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * Create the event listener.
     * HandleBoardItemCreated constructor.
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param  BoardItemCreated  $event
     * @return void
     */
    public function handle(BoardItemCreated $event)
    {
        $boardItem = $event->boardItem;
        $users = $boardItem->project->team()
            ->where('projects_teams.board_read', true)
            ->where(function ($q) {
                $q->where('settings->emails->board_create', true)->orWhere('settings', null);
            })->get();
        $this->mailer->to($users)
                ->send(new BoardItemCreatedMail($event->boardItem));
    }
}
