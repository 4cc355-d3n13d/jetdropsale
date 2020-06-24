<?php

namespace App\Listeners\Order;

use App\Enums\InvoiceStatusType;
use App\Models\Order;

class OrderRejected extends AbstractOrderListener
{
    public function handle(Order $order)
    {
        if ($order->invoice && (int) $order->invoice->status === InvoiceStatusType::OPEN) {
            $order->invoice->calcTotalSum();
            $order->invoice->save();
        }
    }
}
