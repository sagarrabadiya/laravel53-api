<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UsersPolicy
{
    use HandlesAuthorization;

    /**
     * only user itself and admin can update
     * @param User $model
     * @param User $user
     * @return bool
     */
    public function update(User $user, User $model)
    {
        return $model->id === $user->id || $user->admin();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->admin();
    }

    /**
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function delete(User $user, User $model)
    {
        return $user->admin();
    }
}
