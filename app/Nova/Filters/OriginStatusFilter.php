<?php

namespace App\Nova\Filters;

use App\Enums\ShopifyStatusType;
use Illuminate\Http\Request;
use rcknr\Nova\Filters\MultiselectFilter;

class OriginStatusFilter extends MultiselectFilter
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
        return $query->whereIn('origin_status', $value, 'or');
    }

    /**
     * Get the filter's available options.
     * @param \Illuminate\Http\Request $request
     */
    public function options(Request $request): array
    {
        return [
            'Pending'               => ShopifyStatusType::PENDING,
            'Authorized'            => ShopifyStatusType::AUTHORIZED,
            'Partially payed'       => ShopifyStatusType::PARTIALLY_PAID,
            'Payed'                 => ShopifyStatusType::PAID,
            'Partially refunded'    => ShopifyStatusType::PARTIALLY_REFUNDED,
            'Refunded'              => ShopifyStatusType::REFUNDED,
            'Voided'                => ShopifyStatusType::VOIDED,
            'Migrated'              => ShopifyStatusType::MIGRATED
        ];
    }
}
