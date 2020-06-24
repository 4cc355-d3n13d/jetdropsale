<?php
namespace App\Permissions\Rules;

use App\Models\User;

class Rule
{
    protected $name;
    protected $param;
    protected $callback;

    public function __construct(string $name, $param = "*")
    {
        $this->name = $name;
        $this->param = $param;
        $this->init();
    }

    protected function init()
    {
    }

    public function setCallback(\Closure $callback)
    {
        $this->callback = $callback;
    }

    public function hasCallback() : bool
    {
        return !! $this->callback;
    }

    public function callback(User $user, $ability, $params)
    {
        return call_user_func_array($this->callback, func_get_args());
    }

    public function getName()
    {
        return $this->name;
    }


    public function getParam()
    {
        return $this->param;
    }
}
