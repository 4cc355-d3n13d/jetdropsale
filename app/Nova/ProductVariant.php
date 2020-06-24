<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class ProductVariant extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Product\ProductVariant::class;
    public static $displayInNavigation = true;

    public static $search = ['id'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            Avatar::make('image')->thumbnail(function () {
                /** @var \App\Models\Product\ProductVariant $this */
                return $this->image;
            }),
            Text::make('sku'),
            Number::make('amount'),
            Currency::make('price')->format(config('app.money.format'))->sortable(),
            Code::make('combination')->json(),
            Text::make('combination')->onlyOnIndex(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }

    public function title()
    {
        /** @var \App\Models\Product\ProductVariant $this */
        $options = collect($this->options());

        /** @var \App\Models\Product\ProductVariant $this */
        return '[' . implode(',', $options->pluck('value')->values()->toArray()) . '] ' . $this->title;
    }

    /**
     * Build an "index" query for the given resource.
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        if (!$request->get('search', false)) {
            return $query;
        }
        $query->getQuery()->wheres = [];
        $products = \App\Models\Product\Product::search($request->get('search'))->select('id')->take(3)->get()->pluck('id');

        return $query->whereIn('product_id', $products);
    }
}
