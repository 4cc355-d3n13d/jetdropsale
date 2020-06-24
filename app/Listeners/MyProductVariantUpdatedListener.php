<?php

namespace App\Listeners;

use App\Jobs\MarketSync\SyncUpdatedProductToShopify;
use App\Models\Product\MyProductVariant;


class MyProductVariantUpdatedListener
{
    public function handle(MyProductVariant $myProductVariant): bool
    {
        $changeSet = $myProductVariant->getChanges();

        unset(
            $changeSet['updated_at'],
            $changeSet['status'],
            $changeSet['shopify_product_id']
        );

        if (empty($changeSet)) {
            return true;
        }

        if (!empty($myProductVariant->myProduct->user->shops->first()) && $myProductVariant->myProduct->shopify_product_id) {
            SyncUpdatedProductToShopify::dispatch($myProductVariant->myProduct->user->shops->first(), $myProductVariant->myProduct);
        }

        return true;
    }
}
