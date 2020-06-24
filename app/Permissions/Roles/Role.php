<?php

namespace App\Permissions\Roles;

use App\Exceptions\NotRuleException;
use App\Models\User;
use App\Permissions\Rules\Rule;
use Illuminate\Support\Arr;

class Role
{
    /** @var string */
    protected $title;

    /** @var \Illuminate\Support\Collection */
    protected $rules;


    public function __construct(string $name)
    {
        $this->title = $name;
        $this->rules = collect();
        $this->init();
    }

    protected function init()
    {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function add($rules): self
    {
        if (is_string($rules)) {
            $rules = [$rules => '*'];
        }
        if ($rules instanceof Rule) {
            $rules = [$rules];
        }

        throw_unless(is_array($rules), NotRuleException::class);

        $isAssoc = Arr::isAssoc($rules);
        foreach ($rules as $rule => $param) {
            if (is_array($param)) {
                $rule = key($param);
                $param = current($param);
            } elseif (!$isAssoc) {
                $rule = $param;
                $param = '*';
            }
            if (!($rule instanceof Rule)) {
                $rule = new Rule($rule, $param);
            }

            $this->rules = $this->rules->push([
                'param' => $rule->getParam(),
                'name' => $rule->getName(),
                'rule' => $rule
            ]);
        }


        return $this;
    }

    public function check(User $user, $ability, $param): bool
    {
        $checkParam = $param;
        if (is_object($param)) {
            $checkParam = get_class($param);
        }
        foreach ($this->rules->whereIn('name', [$ability, "*"])->whereIn('param', [$checkParam, "*"])->all() as $ruleParam) {
            /** @var Rule $rule */
            $rule = $ruleParam['rule'];
            return $rule->hasCallback() ? $rule->callback($user, $ability, $param) : true;
        }
      
        return false;
    }
}
