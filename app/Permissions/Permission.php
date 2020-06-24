<?php
namespace App\Permissions;

use App\Models\User;
use App\Permissions\Roles\OwnerRole;
use App\Permissions\Roles\Role;

class Permission
{
    public function check(User $user, string $ability, $params)
    {
        $param = array_shift($params);
        /**
         * @var $role Role
         */
        $user->hasRole(new OwnerRole) || $user->addRole(new OwnerRole);
        foreach ($user->getRoles() as $role) {
            if ($role->check($user, $ability, $param)) {
                return true;
            }
        }
        return false;
    }
}
