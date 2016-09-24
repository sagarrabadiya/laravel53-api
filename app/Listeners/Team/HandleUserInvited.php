<?php

namespace App\Listeners\Team;

use App\Events\Team\UserInvited;
use App\Mail\Team\UserInvited as UserInvitedEmail;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleUserInvited implements ShouldQueue
{

    private $mailer;

    /**
     * Create the event listener.
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param  UserInvited  $event
     * @return void
     */
    public function handle(UserInvited $event)
    {
        $this->mailer->to($event->user)
            ->send(new UserInvitedEmail($event->user, $event->project));
    }
}
