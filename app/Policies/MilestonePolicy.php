<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Policies;

use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;

/**
 * Class MilestonePolicy
 * @package App\Policies
 */
class MilestonePolicy
{
    use HandlesAuthorization;

    /**
     * @param User $me
     * @param Milestone $item
     * @param Project $project
     * @return bool
     */
    public function read(User $me, Milestone $item, Project $project)
    {
        return ($me->admin() || $project->pivot->milestone_read);
    }

    /**
     * @param User $me
     * @param Milestone $item
     * @param Project $project
     * @return bool
     */
    public function write(User $me, Milestone $item, Project $project)
    {
        return ($me->admin() || $project->pivot->milestone_write);
    }
}
