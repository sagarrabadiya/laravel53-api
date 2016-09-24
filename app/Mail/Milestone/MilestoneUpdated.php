<?php

namespace App\Mail\Milestone;

use App\Models\Milestone;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MilestoneUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $milestone;

    /**
     * MilestoneCreated constructor.
     * @param Milestone $milestone
     */
    public function __construct(Milestone $milestone)
    {
        $this->milestone = $milestone;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.milestone.updated')
            ->subject('New Milestone has been updated');
    }
}
