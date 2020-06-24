<?php

namespace App\Models\Product;

use App\Enums\ShopifyProductStatusType;
use App\Models\Shopify\Shop;
use App\Models\Shopify\ShopifyProduct;
use App\Services\ShopifyService;
use Log;
use PHPShopify\Exception\ApiException;

trait ShopifyProductTrait
{
    /** @var ShopifyService $client */
    private $client;


    public function exportToShopify(Shop $shop): ShopifyProduct
    {
        /** @var ShopifyProduct $shopifyProduct */
        $shopifyProduct = ! $this->shopifyProduct
            ? ShopifyProduct::create([
                'shopify_id' => null,
                'combination_id' => null,
                'user_id' => $shop->user->id,
                'my_product_id' => $this->id,
                'status' => ShopifyProductStatusType::PENDING,
            ])
            : ShopifyProduct::where('my_product_id', '=', $this->id)->latest('updated_at')->get()->first()
        ;

        $this->setShopifyClient($shop);

        try {
            /** @var MyProduct $this */
            $shopifyProduct->shopify_id = $this->client->exportProduct($this);
            $shopifyProduct->status = ShopifyProductStatusType::OK;
        } catch (\Exception $e) {
            Log::channel('shopify')->error('Не удалось экспортировать продукт', $shopifyProduct->attributesToArray());
            Log::channel('shopify')->error((string) $e);
            $shopifyProduct->status = ShopifyProductStatusType::FAIL;
            $shopifyProduct->save();

            throw new \Exception($e->getMessage(), 0, $e);
        }

        $shopifyProduct->save();

        return $shopifyProduct;
    }

    /**
     * @param Shop $shop
     */
    public function syncWithShopify(Shop $shop, MyProduct $myProduct): MyProduct
    {
        $this->setShopifyClient($shop);

        try {
            $this->client->updateProduct($myProduct);
            $myProduct->status = ShopifyProductStatusType::OK;
        } catch (\Exception $e) {
            Log::channel('shopify')->error('Не удалось сохранить локальные изменения продукта в shopify', $myProduct->attributesToArray());
            Log::channel('shopify')->error((string) $e);
            $myProduct->status = ShopifyProductStatusType::FAIL;
            $myProduct->save();

            throw new \Exception($e->getMessage(), 0, $e);
        }

        $myProduct->save();

        return $myProduct;
    }

    public function removeFromShopify(Shop $shop): ?array
    {
        $this->setShopifyClient($shop);

        try {
            // If we cannot remove @ shopify - remove in ours or just mark with status?
            /** @var MyProduct $this */
            $response = $this->client->deleteProduct($this);
        } catch (ApiException $e) {
            Log::channel('shopify')->error('Не удалось удалить продукт c shopify_product_id = '. optional($this->shopifyProduct)->shopify_id);
            Log::channel('shopify')->error((string) $e);
        } catch (\Throwable $e) {
            Log::channel('shopify')->error('Не удалось удалить продукт c shopify_product_id = '. optional($this->shopifyProduct)->shopify_id);
            Log::channel('shopify')->error((string) $e);

            throw new \Exception($e->getMessage(), 0, $e);
        }

        return $response ?? null;
    }

    public function removeVariantFromShopify(Shop $shop, $myProductVariantId): array
    {
        $this->setShopifyClient($shop);

        try {
            // If we cannot remove @ shopify - remove in ours or just mark with status?
            /** @var MyProduct $this */
            $response = $this->client->deleteProductVariant($this, $myProductVariantId);
        } catch (\Exception $e) {
            Log::channel('shopify')->error("Не удалось удалить вариант продукта c shopify product id = {$this->shopifyProduct->shopify_id}");
            Log::channel('shopify')->error((string) $e);

            throw new \Exception($e->getMessage(), 0, $e);
        }

        return $response;
    }

    private function setShopifyClient(Shop $shop): ShopifyService
    {
        $this->client = app()->make(ShopifyService::class);
        $this->client->setClient($shop);

        return $this->client;
    }
}
