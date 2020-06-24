<?php

namespace App\Http\Transformers;

use App\Models\Invoice;

class InvoiceDetailedTransformer extends InvoiceTransformer
{
    public function transform(Invoice $invoice)
    {
        $transformer = parent::transform($invoice);
        foreach ($invoice->orders as $order) {
            $transformer['orders'][] = $order->id;
        }

        return $transformer;
    }
}
