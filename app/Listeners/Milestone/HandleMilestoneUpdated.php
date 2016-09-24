<?php

namespace App\Listeners\Milestone;

use App\Events\Milestone\MilestoneUpdated;
use App\Mail\Milestone\MilestoneUpdated as MilestoneUpdatedMail;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleMilestoneUpdated implements ShouldQueue
{
    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * HandleMilestoneUpdated constructor.
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param  MilestoneUpdated  $event
     * @return void
     */
    public function handle(MilestoneUpdated $event)
    {
        $milestone = $event->milestone;
        $users = $milestone->project->team()
            ->where('projects_teams.milestone_read', true)
            ->where(function ($q) {
                $q->where('settings->emails->milestone_create', true)->orWhere('settings', null);
            })->get();
        $this->mailer->to($users)
            ->send(new MilestoneUpdatedMail($milestone));
    }
}
