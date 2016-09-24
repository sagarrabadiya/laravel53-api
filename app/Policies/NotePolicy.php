<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Policies;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;
use App\Models\Project;

class NotePolicy
{
    use HandlesAuthorization;


    /**
     * @param User $me
     * @param Model $item
     * @param Project $project
     * @return bool
     */
    public function read(User $me, Model $item, Project $project)
    {
        return ($me->admin() || $project->pivot->note_read);
    }

    /**
     * @param User $me
     * @param Model $item
     * @param Project $project
     * @return bool
     */
    public function write(User $me, Model $item, Project $project)
    {
        return ($me->admin() || $project->pivot->note_write);
    }
}
