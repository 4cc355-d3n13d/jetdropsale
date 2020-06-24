<?php

namespace App\Listeners\Order;

use App\Jobs\DeferredOrderPending;
use App\Models\Order;

class OrderHold
{
    public function handle(Order $order)
    {
        DeferredOrderPending::dispatch($order)->delay(
            now()->addMinutes($order->user->setting('order_hold_time') + 1) // just to be sure
        );
    }
}
