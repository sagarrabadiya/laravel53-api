<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Core\Transformers;

use App\Models\User;

/**
 * Class UserTransformer
 * @package App\Transformers
 */
class UserTransformer extends Transformer
{
    /**
     * @param $user
     * @return array
     */
    public function transform(User $user)
    {
        $responseData = [
            'id'    =>    $user->id,
            'username'    =>    $user->username,
            'admin'    =>    $user->designation === 'admin',
            'firstname'    =>    $user->firstname,
            'lastname'    =>    $user->lastname,
            'email'    =>    $user->email,
            'designation' => $user->designation,
            'avatar'    =>    $user->avatar,
            'created_at'    =>    $user->created_at->toIso8601String(),
            'updated_at'    =>    $user->updated_at->toIso8601String()
        ];
        
        return $responseData;
    }
}
