<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class ShipGoods extends Setting implements GoodsContract
{
    const KEY = 'ship.goods.price';

    protected $appends = ['price', 'title'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('ship_cost', function (Builder $builder) {
            $builder->where('key', '=', self::KEY);
        });
    }

    public function getAmount()
    {
        return 1;
    }

    public function getPrice()
    {
        return self::get()->first()->value;
    }

    public function getPriceAttribute()
    {
        return $this->getPrice();
    }

    public function getImage()
    {
        return '';
    }

    public function getTitle()
    {
        return 'Ship cost';
    }

    public function getTitleAttribute()
    {
        return $this->getTitle();
    }
}
