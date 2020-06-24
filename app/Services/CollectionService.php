<?php

namespace App\Services;

use App\Models\Product\MyProductCollection;
use App\Models\Shopify\Shop;
use App\Models\Shopify\ShopifyProduct;

class CollectionService
{
    /** @var ShopifyService */
    public $shopify;


    public function __construct(Shop $shop)
    {
        $this->setClient($shop);
    }

    private function setClient(Shop $shop): ShopifyService
    {
        /** @var ShopifyService $shopify */
        $shopify = app()->make(ShopifyService::class);
        $shopify->setClient($shop);

        return $this->shopify = $shopify;
    }

    protected function getClient()
    {
        return $this->shopify->getClient();
    }

    public function index()
    {
        // GET /admin/collects.json
        // Retrieves a list of collects
    }

    public function collectProduct(MyProductCollection $collection, ShopifyProduct $product)
    {
        return $this->getClient()->Collect()->post([
            'product_id' => $product->id,
            'collection_id' => $collection->id,
        ]);
    }

    public function removeProduct($collection, $product)
    {
        // DELETE /admin/collects/#{collect_id}.json
        // Removes a product from a collection
    }

    public function create(string $title): array
    {
        return $this->getClient()->CustomCollection()->post(['title' => $title]);
    }

    public function delete($shopifyId)
    {
        return $this->getClient()->CustomCollection($shopifyId)->delete();
    }
}
