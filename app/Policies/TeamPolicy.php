<?php
/**
 * Created by PhpStorm.
 * User: sagar
 * Date: 28/05/16
 * Time: 11:32 PM
 */

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;
use App\Models\Project;

class TeamPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function writeMember(User $user, Project $project)
    {
        return $user->admin() or $project->pivot->team_write;
    }

    /**
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function readMember(User $user, Project $project)
    {
        return $user->admin() or $project->pivot->team_read or $project->pivot->user_id === $user->id;
    }

    /**
     * Determine whether the user can create projects.
     * @param User $user
     * @return boolean
     */
    public function create(User $user)
    {
        return $user->admin();
    }

    /**
     * Determine whether the user can update the project.
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    public function update(User $user, Project $project)
    {
        return $user->admin();
    }

    /**
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    public function delete(User $user, Project $project)
    {
        return $user->admin();
    }
}
