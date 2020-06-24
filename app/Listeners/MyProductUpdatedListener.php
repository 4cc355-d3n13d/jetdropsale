<?php

namespace App\Listeners;

use App\Jobs\MarketSync\SyncUpdatedProductToShopify;
use App\Models\Product\MyProduct;


class MyProductUpdatedListener
{
    public function handle(MyProduct $updatedMyProduct): bool
    {
        $changeSet = $updatedMyProduct->getChanges();

        unset(
            $changeSet['updated_at'],
            $changeSet['status'],
            $changeSet['shopify_product_id']
        );

        if (empty($changeSet)) {
            return true;
        }

        if (!empty($updatedMyProduct->user->shops->first()) && $updatedMyProduct->shopify_product_id) {
            SyncUpdatedProductToShopify::dispatch($updatedMyProduct->user->shops->first(), $updatedMyProduct);
        }

        return true;
    }
}
