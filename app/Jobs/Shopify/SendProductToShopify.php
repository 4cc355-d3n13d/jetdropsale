<?php

namespace App\Jobs\Shopify;

use App\Enums\MyProductStatusType;
use App\Models\Product\MyProduct;
use App\Models\Shopify\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendProductToShopify implements ShouldQueue
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
        $this->queue = 'high';
    }

    public function handle(): void
    {
        $shopifyProduct = $this->myProduct->exportToShopify($this->shop);
        is_array($this->myProduct->images) && $this->myProduct->images = json_encode($this->myProduct->images);
        $this->myProduct->update([
            'shopify_product_id' => $shopifyProduct->id,
            'status' => MyProductStatusType::CONNECTED,
        ]);
    }
}
