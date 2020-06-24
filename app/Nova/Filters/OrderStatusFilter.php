<?php

namespace App\Nova\Filters;

use App\Enums\OrderStatusType;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use rcknr\Nova\Filters\MultiselectFilter;

class OrderStatusFilter extends Filter
{
    /**
     * Apply the filter to the given query.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\HasMany  $query
     * @param  mixed  $value
     * @return  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->where('status', $value);
    }

    /**
     * Get the filter's available options.
     * @param \Illuminate\Http\Request $request
     */
    public function options(Request $request): array
    {
        return OrderStatusType::getNovaStatusList();
    }
}
