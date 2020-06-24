<?php

namespace App\Listeners\Order;

use App\Enums\OrderStatusType;
use App\Models\CreditLimit;
use App\Models\Invoice;

class InvoicePaid extends AbstractOrderListener
{
    public function handle(Invoice $invoice)
    {
        $user = $invoice->user;
        if ($newCreditLimit =
            CreditLimit::where('limit', '>', $user->credit_limit)
                ->orderBy('limit', 'asc')->get()->first()
        ) {
            $user->update(['credit_limit' => $newCreditLimit->limit]);
            $this->log(sprintf('Credit limit for user %s has been increased (now: %d)', $user->email, $newCreditLimit));
        }

        $invoice->orders()->where(['status'=>OrderStatusType::REJECTED_INVOICE])->get()->each->changeStatus(OrderStatusType::CONFIRMED, true, true);
    }
}
