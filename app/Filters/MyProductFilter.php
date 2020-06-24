<?php

namespace App\Filters;

use App\Enums\MyProductStatusType;
use Illuminate\Database\Eloquent\Builder;

class MyProductFilter extends AbstractFilter
{
    protected $filters = [
        'product_status',
    ];


    protected function productStatus(string $status): Builder
    {
        switch ($status) {
            case 'connected':
                $this->builder->where(['status' => MyProductStatusType::CONNECTED])->latest('connected_at');
                break;
            case 'non_connected':
                $this->builder->where('status', '<>', MyProductStatusType::CONNECTED)->latest();
                break;

        }
        return $this->builder;
    }
}
