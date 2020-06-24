<?php

namespace App\Permissions;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModelPolicy
{
    use HandlesAuthorization;

    /**
     * Proxy to Permission::check
     * @param $user
     * @param $ability
     * @param $model
     * @return mixed
     */
    public function before($user, $ability, $model)
    {
        return $user->can($ability, $model);
    }


    // need for Nova
    public function viewAny()
    {
    }
}
