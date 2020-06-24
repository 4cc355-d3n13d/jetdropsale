<?php
namespace App\Permissions\Roles;

use App\Permissions\Rules\AdminRule;

class AdminRole extends Role
{
    public function __construct()
    {
        parent::__construct($name = "Admin");
    }

    public function init()
    {
        $this->add(new AdminRule);
    }
}
