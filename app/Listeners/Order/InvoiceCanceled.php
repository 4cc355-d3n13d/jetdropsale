<?php

namespace App\Listeners\Order;

use App\Enums\OrderStatusType;
use App\Models\Invoice;

class InvoiceCanceled
{
    public function handle(Invoice $invoice)
    {
        $invoice->orders()
            ->whereNotIn('status', [OrderStatusType::SHIPPED, OrderStatusType::DELIVERED, OrderStatusType::CREATED])->get()
            ->each->changeStatus(OrderStatusType::CANCELLED, true, true);
    }
}
