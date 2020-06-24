<?php

namespace App\Jobs;

use App\Models\Product\ProductDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class StoreProductDetails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $details;
    protected $product_id;

    /**
     * Create a new job instance.
     *
     * @param array $details
     * @param int $product_id
     */
    public function __construct(array $details, int $product_id)
    {
        $this->product_id = $product_id;
        $this->details = $details;
        $this->queue = 'low';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        collect($this->details)->each(function ($value, $title) {
            tap(ProductDetail::firstOrNew(['product_id'=>$this->product_id, 'title' => $title]), function (ProductDetail $specific) use ($value) {
                $specific->value = $value;
                $specific->save();
            });
        });
    }
}
