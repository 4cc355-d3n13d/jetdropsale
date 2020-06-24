<?php

namespace App\Listeners\Order;

use App\Enums\OrderOriginType;
use App\Enums\OrderStatusType;
use App\Events\OrderCreatedEvent;
use App\Jobs\DeferredOrderPending;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

/**
 * Class OrderCreatedListener
 */
class OrderCreatedListener extends AbstractOrderListener
{
    public function handle(Order $order): void
    {
        $this->log([
            'New order #%d from %s received for user %s',
            $order->origin_id,
            OrderOriginType::getDescription($order->origin),
            $order->user->email
        ]);

        $order->changeStatus(OrderStatusType::HOLD);
        return;
    }
}
