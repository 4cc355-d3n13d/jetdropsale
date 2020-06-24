<?php

namespace App\Filters;

use App\Enums\OrderStatusType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class OrderFilters extends AbstractFilter
{
    /**
     * Registered filters to operate upon.
     * @var array
     */
    protected $filters = [
        'id',
        'originId',
        'trackingNumber',
        'title',
        'status',
        'startDate',
        'endDate',
    ];


    protected function id(int $id): Builder
    {
        return $this->builder->where('orders.id', $id);
    }

    /** @param int|string $id */
    protected function originId($id): Builder
    {
        return $this->builder->where('origin_id', $id);
    }

    /** @param int|string $id */
    protected function trackingNumber($id): Builder
    {
        return $this->builder->where('tracking_number', $id);
    }

    protected function title(string $subtitle): Builder
    {
        return $this->builder
            ->select('orders.*')
            ->join('order_cart', 'order_cart.order_id', '=', 'orders.id')
            ->where('order_cart.title', 'like', '%' . $subtitle . '%')
            ->groupBy('orders.id')
        ;
    }

    protected function status(string $statusString): Builder
    {
        if (! key_exists($statusString, OrderStatusType::toArray())) {
            return $this->builder;
        }

        return $this->builder->where('status', OrderStatusType::getValue($statusString));
    }

    protected function startDate($date): Builder
    {
        return $this->builder->where('created_at', '>=', new Carbon($date));
    }

    protected function endDate($date): Builder
    {
        return $this->builder->where('created_at', '<=', new Carbon($date));
    }
}
