<?php

namespace App\Jobs\MarketSync;

use App\Models\Product\MyProduct;
use App\Models\Shopify\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncUpdatedProductToShopify implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var MyProduct */
    private $myProduct;

    /** @var Shop */
    private $shop;


    public function __construct(Shop $shop, MyProduct $myProduct)
    {
        $this->shop = $shop;
        $this->myProduct = $myProduct;
    }

    public function handle(): void
    {
        $this->myProduct->syncWithShopify($this->shop, $this->myProduct);
    }
}
