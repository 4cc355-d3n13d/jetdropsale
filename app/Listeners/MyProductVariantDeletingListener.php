<?php

namespace App\Listeners;

use App\Jobs\MarketSync\SyncRemovedMyProductVariantsToShopify;
use App\Models\Product\MyProductVariant;
use App\Models\Shopify\ProductVariant as ShopifyProductVariant;


class MyProductVariantDeletingListener
{
    public function handle(MyProductVariant $myProductVariant): bool
    {
        if (!empty($myProductVariant->myProduct->user->shops->first()) && $myProductVariant->myProduct->shopify_product_id) {
            $shopifyProductVariant = ShopifyProductVariant::where(
                [
                    'product_id' => $myProductVariant->myProduct->id,
                    'product_variant_id' => $myProductVariant->id,
                ]
            )->firstOrFail();

            SyncRemovedMyProductVariantsToShopify::dispatch(
                $myProductVariant->myProduct->user->shops->first(),
                $myProductVariant->myProduct,
                $shopifyProductVariant->shopify_variant_id
            );
        }

        return true;
    }
}
