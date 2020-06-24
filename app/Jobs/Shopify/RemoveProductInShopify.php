<?php

namespace App\Jobs\Shopify;

use App\Models\Product\MyProduct;
use App\Models\Shopify\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use PHPShopify\Exception\ApiException;

class RemoveProductInShopify implements ShouldQueue
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
        try {
            $this->myProduct->removeFromShopify($this->shop);
        } catch (ApiException $e) {
            if ($e->getMessage() == "Not Found") {
                return;
            }
            throw $e;
        }
    }
}
