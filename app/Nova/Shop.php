<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Inspheric\Fields\Url;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;

class Shop extends Resource
{
    public static $model = \App\Models\Shopify\Shop::class;

    public static $group = 'Manage';

    public static $search = [
        'id', 'shop' , 'user_id'
    ];

    public function fields(Request $request)
    {
        return [
            ID::make('id')->sortable(),
            Url::make('shop', function () {
                return route('data.shopify.orders', ['shop' => optional($this)->shop]);
            })
                ->label(optional($this)->shop)
                ->clickableOnIndex()
                ->sortable()
                ->clickable(),

            DateTime::make('created_at')->sortable(),

            HasOne::make('User'),
        ];
    }
}
