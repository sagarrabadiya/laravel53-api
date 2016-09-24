<?php

namespace App\Listeners\Milestone;

use App\Events\Milestone\MilestoneCreated;
use App\Mail\Milestone\MilestoneCreated as MilestoneCreatedMail;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleMilestoneCreated implements ShouldQueue
{

    protected $mailer;

    /**
     * HandleMilestoneCreated constructor.
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param  MilestoneCreated  $event
     * @return void
     */
    public function handle(MilestoneCreated $event)
    {
        $milestone = $event->milestone;
        $users = $milestone->project->team()
            ->where('projects_teams.milestone_read', true)
            ->where(function ($q) {
                $q->where('settings->emails->milestone_create', true)->orWhere('settings', null);
            })->get();
        $this->mailer->to($users)
            ->send(new MilestoneCreatedMail($milestone));
    }
}
