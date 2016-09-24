<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Core\Transformers;

use App\Models\User;

/**
 * Class ProjectTeamTransformer
 * @package App\Transformers
 */
class TeamTransformer extends Transformer
{
    /**
     * @param $user
     * @return array
     */
    public function transform(User $user)
    {
        $response = (new UserTransformer())->transform($user);
        $response['permissions'] = $user->pivot;
        // convert permissions to boolean
        if ($user->pivot->role) {
            $response['permissions']['role'] = $user->pivot->role->name;
        }
        if ($user->pivot->settings) {
            $response['settings'] = gettype($user->pivot->settings) == 'string'
                ? json_decode($user->pivot->settings)
                : $user->pivot->settings;
        }
        return $response;
    }
}
