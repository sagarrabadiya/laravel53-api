<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Core\Transformers;

use App\Models\Role;

/**
 * Class ProjectRoleTransformer
 * @package App\Transformers
 */
class RoleTransformer extends Transformer
{
    /**
     * @param $role
     * @return array
     */
    public function transform(Role $role)
    {
        return [
            'id'    => $role->id,
            'name'    =>    $role->name,
            'board_read'    =>    $role->board_read,
            'board_write'    =>    $role->board_write,
            'milestone_read'    =>    $role->milestone_read,
            'milestone_write'    =>    $role->milestone_write,
            'ticket_read'    =>    $role->ticket_read,
            'ticket_write'    =>    $role->ticket_write,
            'note_read'    =>    $role->note_read,
            'note_write'    =>    $role->note_write,
            'team_read'    =>    $role->team_read,
            'team_write'    =>    $role->team_write,
            'created_at'    =>    $role->created_at->toIso8601String(),
            'updated_at'    =>    $role->updated_at->toIso8601String(),
        ];
    }
}
