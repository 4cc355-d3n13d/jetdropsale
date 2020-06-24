<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatusType;
use App\Jobs\ProcessInvoicePayment;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class BillingSchedule
 */
class BillingSchedule extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'billing:schedule';

    /**
     * The console command description.
     */
    protected $description = 'Charges the expired invoices. Command to run via crontab every 24h.';


    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $invoices = Invoice::where([
            'status' => InvoiceStatusType::AWAITING_PAYMENT,
        ])->orWhere(function (Builder $builder) {
            $builder->orWhere('status', InvoiceStatusType::OPEN);
            $builder->where('expire_at', '<', new Carbon());
        })->get();

        foreach ($invoices as $invoice) {
            ProcessInvoicePayment::dispatch($invoice);
        }
    }
}
