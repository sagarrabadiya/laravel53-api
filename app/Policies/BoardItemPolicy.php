<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Policies;

use App\Models\BoardItem;
use App\Models\Project;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;

/**
 * Class BoardItemPolicy
 * @package App\Policies
 */
class BoardItemPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $me
     * @param BoardItem $item
     * @param Project $project
     * @return bool
     */
    public function read(User $me, BoardItem $item, Project $project)
    {
        return ($me->admin() || $project->pivot->board_read);
    }

    /**
     * @param User $me
     * @param BoardItem $item
     * @param Project $project
     * @return bool
     */
    public function write(User $me, BoardItem $item, Project $project)
    {
        return ($me->admin() || $project->pivot->board_write);
    }
}
