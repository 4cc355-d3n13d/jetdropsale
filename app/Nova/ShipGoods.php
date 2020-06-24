<?php

namespace App\Nova;

use App\Models\ShipGoods as ShipGoodsModel;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class ShipGoods extends Resource
{
    public static $model = ShipGoodsModel::class;

    public static $displayInNavigation = false;

    public function fields(Request $request)
    {
        return [
            Text::make('title'),
            Text::make('price'),

        ];
    }


    public function title()
    {
        return $this->title . " = " . $this->price;
    }
}
