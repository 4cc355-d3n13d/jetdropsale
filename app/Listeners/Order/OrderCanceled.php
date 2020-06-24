<?php

namespace App\Listeners\Order;

use App\Enums\InvoiceStatusType;
use App\Models\Order;

class OrderCanceled
{
    public function handle(Order $order)
    {
        $invoice = $order->invoice;
        if ($invoice && $invoice->status != InvoiceStatusType::PAID) {
            $invoice->calcTotalSum();
            $invoice->save();
        }
    }
}
