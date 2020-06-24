<?php

namespace App\Services;

use App\Models\User;

class PriceModifierService {

    private $rate;
    private $type;

    public function getRate()
    {
        return $this->rate;
    }

    public function setRate($rate): PriceModifierService
    {
        $this->rate = $rate;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): PriceModifierService
    {
        $this->type = $type;
        return $this;
    }

    public function __construct(User $user)
    {
        if ($user->setting('gpr_rate')) {
            $this->setRate(
                $user->setting('gpr_rate')
            );
        };

        if ($user->setting('gpr_type')) {
            $this->setType(
                $user->setting('gpr_type')
            );
        };
    }

    public function modify($price)
    {
        switch ($this->getType()) {
            case 'f':
                return $price + $this->getRate();
            case 'm';
                return $price * $this->getRate();
            default:
                return $price;
        }
    }
}
