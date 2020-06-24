<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Services\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessInvoicePayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Invoice */
    protected $invoice;


    public function __construct(Invoice $invoice)
    {
        $this->queue = 'high';
        $this->invoice = $invoice;
    }

    /**
     * Execute the job.
     * @throws \InvalidArgumentException
     */
    public function handle(PaymentService $paymentService): void
    {
        $paymentService->payInvoice($this->invoice);
    }

    /**
     * The job failed to process.
     */
    public function failed(\Exception $exception): void
    {
        // Send user notification of failure, etc...
    }
}
