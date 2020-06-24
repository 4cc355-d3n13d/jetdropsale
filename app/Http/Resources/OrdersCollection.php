<?php

namespace App\Http\Resources;

class OrdersCollection extends Collection
{
    public $collects = OrderItem::class;
}
