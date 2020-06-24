<?php

namespace App\Listeners\Order;

use App\Enums\InvoiceStatusType;
use App\Enums\OrderStatusType;
use App\Models\Invoice;

class InvoiceChangeSum extends AbstractOrderListener
{
    public function handle(Invoice $invoice)
    {
        // What we should to do if the invoice has 0 price? Remove for now. Can be restored later...
        if ($invoice->total_sum <= 0 && $invoice->orders()->where('status', '!=', OrderStatusType::CANCELLED)->doesntExist()) {
            ! in_array($invoice->status, [InvoiceStatusType::PAID, InvoiceStatusType::REFUNDED]) && $invoice->delete();
        }
    }
}
