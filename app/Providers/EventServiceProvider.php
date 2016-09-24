<?php

namespace App\Providers;

use App\Events\BoardItem\BoardItemCreated;
use App\Events\BoardItem\BoardItemUpdated;
use App\Events\Comment\CommentCreated;
use App\Events\Milestone\MilestoneCreated;
use App\Events\Milestone\MilestoneUpdated;
use App\Events\Note\NoteCreated;
use App\Events\Note\NotePageCreated;
use App\Events\Note\NotePageUpdated;
use App\Events\Note\NoteUpdated;
use App\Events\Team\UserCreated;
use App\Events\Team\UserInvited;
use App\Events\Ticket\TicketAssigneeChanged;
use App\Events\Ticket\TicketStatusChanged;
use App\Events\Ticket\TicketUpdated;
use App\Listeners\BoardItem\HandleBoardItemCreated;
use App\Listeners\BoardItem\HandleBoardItemUpdated;
use App\Listeners\Comment\HandleCommentCreated;
use App\Listeners\Milestone\HandleMilestoneCreated;
use App\Listeners\Milestone\HandleMilestoneUpdated;
use App\Listeners\Note\HandleNoteCreated;
use App\Listeners\Note\HandleNotePageCreated;
use App\Listeners\Note\HandleNotePageUpdated;
use App\Listeners\Note\HandleNoteUpdated;
use App\Listeners\Team\HandleUserInvited;
use App\Listeners\Team\HandleUserCreated;
use App\Listeners\Ticket\HandleTicketAssigneeChanged;
use App\Listeners\Ticket\HandleTicketCreated;
use App\Listeners\Ticket\HandleTicketStatusChanged;
use App\Listeners\Ticket\HandleTicketUpdated;
use App\Mail\Ticket\TicketCreated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserCreated::class => [ HandleUserCreated::class ],
        UserInvited::class => [ HandleUserInvited::class ],
        BoardItemCreated::class => [ HandleBoardItemCreated::class ],
        BoardItemUpdated::class => [ HandleBoardItemUpdated::class ],
        MilestoneCreated::class => [ HandleMilestoneCreated::class ],
        MilestoneUpdated::class => [ HandleMilestoneUpdated::class ],
        NoteCreated::class => [ HandleNoteCreated::class ],
        NoteUpdated::class => [ HandleNoteUpdated::class ],
        NotePageCreated::class => [ HandleNotePageCreated::class ],
        NotePageUpdated::class => [ HandleNotePageUpdated::class ],
        TicketCreated::class => [ HandleTicketCreated::class ],
        TicketUpdated::class => [ HandleTicketUpdated::class ],
        TicketStatusChanged::class => [ HandleTicketStatusChanged::class ],
        TicketAssigneeChanged::class => [ HandleTicketAssigneeChanged::class ],
        CommentCreated::class => [ HandleCommentCreated::class ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
