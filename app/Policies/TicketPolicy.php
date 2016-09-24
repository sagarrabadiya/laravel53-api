<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Policies;

use App\Models\Project;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;
use App\Models\Ticket;

/**
 * Class TicketPolicy
 * @package App\Policies
 */
class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $me
     * @param Ticket $item
     * @param Project $project
     * @return bool
     */
    public function read(User $me, Ticket $item, Project $project)
    {
        return ($me->admin() || $project->pivot->ticket_read);
    }

    /**
     * @param User $me
     * @param Ticket $item
     * @param Project $project
     * @return bool
     */
    public function write(User $me, Ticket $item, Project $project)
    {
        return ($me->admin() || $project->pivot->ticket_write);
    }
}
