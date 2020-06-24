<?php

namespace App\Listeners\Order;

use App\Enums\InvoiceStatusType;
use App\Enums\OrderStatusType;
use App\Models\Invoice;
use App\Models\Order;

class OrderPending extends AbstractOrderListener
{
    public function handle(Order $order)
    {
        $price = $order->price;
        $order->reCalcPrice();
        if ($price != $order->price) {
            $order->save();
            if ($order->invoice) {
                $order->invoice->calcTotalSum();
                $order->invoice->save();
            }
        }
        // If the user has no card set the corresponding status
        if (! $order->user->cards()->where('primary', true)->exists()) {
            return $order->changeStatus(OrderStatusType::NO_CARD, true, true);
        }

        if (! $order->invoice) {
            $invoice = $order->user->getOpenInvoice();

            // If we have the new invoice - trying to pay old ones
            if ($invoice->wasRecentlyCreated) {
                Invoice
                    ::where(['user_id' => $invoice->user_id])
                    ->where('status', InvoiceStatusType::REJECTED)->get()
                    ->each->changeStatus(InvoiceStatusType::AWAITING_PAYMENT, true)
                ;
            };
            $invoice->addOrder($order);

            $this->log(['Order #%d was added to the invoice #%d which belongs to user %s', $order->id, $invoice->id, $order->user->email]);
            $invoice->refresh();
        } else {
            $invoice = $order->invoice;
        }

        if (InvoiceStatusType::REJECTED == $invoice->status) {
            $order->changeStatus(OrderStatusType::REJECTED_INVOICE, true, true);
        } else {
            $order->changeStatus(OrderStatusType::CONFIRMED, true, true);
        }
    }
}
