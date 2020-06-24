<?php

namespace App\Nova;

use Carlson\NovaLinkField\Link;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Trix;

class MyProduct extends Resource
{
    /**
     * The model the resource corresponds to.
     */
    public static $model = \App\Models\Product\MyProduct::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     */
    public static $search = [
        'id',
    ];

    public static $displayInNavigation = false;

    /**
     * Get the fields displayed by the resource.
     */
    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),
            ID::make('user_id')->sortable()->onlyOnIndex(),
            Textarea::make('title')->alwaysShow(),

            Image::make('image')->thumbnail(function () {
                return $this->image;
            })->preview(function () {
                return $this->image;
            }),

            Text::make('title', function () {
                return str_limit($this->title, 35);
            })->onlyOnIndex(),

            Text::make('images', function () {
                if (json_decode($this->images)) {
                    return view('admin.images', ['imgs' => json_decode($this->images)])->render();
                }
            })->asHtml()->hideFromIndex(),

            Link::make('Ali link')
                ->details([
                    'href' => function () {
                        return 'https://alitems.com/g/1e8d1144949591a4efe116525dc3e8/?i=5&ulp=https://www.aliexpress.com/item//' . optional($this)->ali_id . '.html&aff_short_key=6iEY3FE';
                    },
                    'text' => function () {
                        return optional($this)->ali_id;
                    },
                    'newTab' => true
                ])->onlyOnIndex(),

            ID::make('product_id'),
            ID::make('shopify_product_id')->onlyOnDetail(),
            ID::make('ali_id')->hideFromIndex(),

            Number::make('amount'),
            Trix::make('description')->hideFromIndex(),

            Currency::make('Price')->format(config('app.money.format'))->sortable(),

            DateTime::make('created_at')->sortable(),

            HasMany::make('Options', null, MyProductOption::class),
            HasMany::make('Combinations', null, MyProductVariant::class)
        ];
    }

    /**
     * Get the filters available for the resource.
     * @param Request $request
     * @return array
     */
    public function filters(Request $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     */
    public function lenses(Request $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     */
    public function actions(Request $request): array
    {
        return [];
    }
}
