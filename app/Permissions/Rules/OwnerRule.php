<?php
namespace App\Permissions\Rules;

use App\Models\User;
use App\SuperClass\Model;

class OwnerRule extends Rule
{
    public function init()
    {
        $this->setCallback(function (User $user, $ability, $param) {
            if (isset($param) && is_object($param) && $param instanceof Model && isset($param->user_id)) {
                return (int) $user->id === (int) $param->user_id;
            }

            return false;
        });
    }
}
