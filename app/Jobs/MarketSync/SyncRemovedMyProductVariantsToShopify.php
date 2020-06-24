<?php

namespace App\Jobs\MarketSync;

use App\Models\Product\MyProduct;
use App\Models\Shopify\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncRemovedMyProductVariantsToShopify implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var MyProduct */
    private $myProduct;

    /** @var Shop */
    private $shop;

    private $myProductVariantId;

    public function __construct(Shop $shop, MyProduct $myProduct, $myProductVariantId)
    {
        $this->shop = $shop;
        $this->myProduct = $myProduct;
        $this->myProductVariantId = $myProductVariantId;
    }

    public function handle(): void
    {
        $this->myProduct->removeVariantFromShopify($this->shop, $this->myProductVariantId);

    }
}
