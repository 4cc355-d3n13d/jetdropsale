<?php

namespace App\Listeners\Order;

use App\Enums\OrderStatusType;
use App\Models\Invoice;

class InvoiceRejected
{
    public function handle(Invoice $invoice)
    {
        $invoice->orders()
            ->where('status', OrderStatusType::CONFIRMED)->get()
            ->each->changeStatus(OrderStatusType::REJECTED_INVOICE, true, true);
    }
}
