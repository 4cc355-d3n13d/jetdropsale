<?php

namespace App\Nova;

use App\Models\Product\Category as CategoryModel;
use App\Nova\Filters\CategoryFilter;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Category extends Resource
{
    /**
     * The model the resource corresponds to.
     */
    public static $model = CategoryModel::class;

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

    /**
     * Get the fields displayed by the resource.
     */
    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),
            Number::make('sort')->sortable(),
            Text::make('title'),
            Text::make('icon'),
            HasOne::make('Category', 'parent')
        ];
    }

    /**
     * Get the cards available for the request.
     */
    public function cards(Request $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     */
    public function filters(Request $request): array
    {
        return [
            new CategoryFilter()
        ];
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

    /**
     * Build a Scout search query for the given resource.
     *
     * @param NovaRequest $request
     * @param  \Laravel\Scout\Builder $query
     * @return \Laravel\Scout\Builder
     */
    public static function scoutQuery(NovaRequest $request, $query)
    {
        return  Category::search($request->get('products_search'));
    }
}
