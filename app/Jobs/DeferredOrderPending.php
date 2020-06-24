<?php

namespace App\Jobs;

use App\Enums\InvoiceStatusType;
use App\Enums\OrderStatusType;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeferredOrderPending implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Order */
    public $order;

    /** @var bool */
    public $saveOrder;

    /**
     * Create a new job instance.
     * @param bool $saveOrder Should be false inside the Order Updating(ed) Listener
     */
    public function __construct(Order $order, bool $saveOrder = true)
    {
        $this->order = $order;
        $this->saveOrder = $saveOrder;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = $this->order;

        // Auto-confirmation...
        if ($order->status === OrderStatusType::HOLD && $order->auto_confirm_at <= now()) {
            $order->changeStatus(OrderStatusType::PENDING, $this->saveOrder);
        }
    }
}
