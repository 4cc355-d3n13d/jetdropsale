<?php

namespace App\Listeners\Order;

use App\Jobs\ProcessInvoicePayment;
use App\Models\Invoice;

class InvoiceAwaitingPayment
{
    public function handle(Invoice $invoice)
    {
        // Lets recalc to be sure
        $invoice->calcTotalSum();
        $invoice->save();
        ProcessInvoicePayment::dispatch($invoice);
    }
}
