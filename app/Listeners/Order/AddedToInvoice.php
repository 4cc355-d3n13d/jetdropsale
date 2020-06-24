<?php

namespace App\Listeners\Order;

use App\Enums\InvoiceStatusType;
use App\Models\Invoice;

class AddedToInvoice extends AbstractOrderListener
{
    public function handle(Invoice $invoice)
    {
        if ($invoice->isLimitOverrun()) {
            $invoice->changeStatus(InvoiceStatusType::AWAITING_PAYMENT, true);
        }
    }
}
