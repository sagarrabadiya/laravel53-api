<?php

namespace App\Listeners\Team;

use App\Events\Team\UserCreated;
use App\Mail\Team\UserCreated as UserCreatedEmail;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleUserCreated implements ShouldQueue
{

    protected $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param  UserCreated  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $this->mailer->to($event->user)->send(new UserCreatedEmail($event->user));
    }
}
