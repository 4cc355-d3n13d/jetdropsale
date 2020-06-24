<?php
namespace App\Permissions\Rules;

class AdminRule extends Rule
{
    public function __construct(string $name = "*", $param = "*")
    {
        parent::__construct($name, $param);
    }
}
