<?php

namespace App\Services;

use App\Models\Product\MyProduct;
use App\Models\Product\MyProductCollection;
use App\Models\Product\MyProductOption;
use App\Models\Product\MyProductTag;
use App\Models\Product\Product;
use App\Models\Product\ProductOption;
use App\Models\Product\ProductVariant;
use App\Models\Shopify;
use App\Models\Shopify\ProductVariant as ShopifyProductVariant;
use App\Models\Shopify\Shop;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Support\Collection;
use PHPShopify\Exception\ApiException;
use PHPShopify\ShopifySDK;

/**
 * Class ShopifyService
 */
class ShopifyService
{
    protected const DEFAULT_VARIANT_BATCH_SIZE = 100;
    protected const DEFAULT_VARIANT_NAME = 'Default';
    protected const TAGS_DELIMITER = ', ';

    /** @var ShopifySDK */
    protected $client;

    /** @var Shop */
    protected $shop;

    /** @var MyProduct */
    protected $product;


    public function setClient(Shop $shop): ShopifyService
    {
        if ($this->shop !== $shop) {
            $this->shop = $shop;
        }

        $token = $this->shop->getAccessToken();
        $config = ['ShopUrl' => $shop->shop, 'AccessToken' => $token];

        $this->client = new ShopifySDK($config);
        return $this;
    }

    public function getClient(): ShopifySDK
    {
        return $this->client;
    }

    public function webhook(array $hook): array
    {
        return $this->client->Webhook->post($hook);
    }

    public function exportProduct(MyProduct $product): int
    {
        $this->product = $product;
        [$productData, $optImages] = $this->prepare($product);
        $sku = new Hashids($product->id);

        if (! count($productData['variants'])) {
            $productData['variants'] = [[
                'option1' => self::DEFAULT_VARIANT_NAME,
                'price' => $product->price,
                'sku' => $sku->encode($product->id),
                'inventory_management' => 'dropwow',
                'fulfillment_service' => 'dropwow',
                'inventory_quantity' => $product->amount,
            ]];
            $optImages = collect([self::DEFAULT_VARIANT_NAME => $product->image]);
        }

        $response = $this->client->Product->post($productData);

        unset($productData['body_html']);

        $this->saveVariantsToDatabase(collect($response));

        if ($product->combinations()->count() > 0 && $optImages) {
            $this->variantsImages(collect($response), $optImages);
        }

        //$this->exportProductCollections($product, $response['id']); remove collections

        return $response['id'];
    }

    protected function exportProductCollections(MyProduct $product, int $shopifyProductId)
    {
        foreach ($product->collections as $collection) {
            /** @var MyProductCollection $collection */
            if (! $collection->shopifyCollection) {
                $response = $this->client->CustomCollection->post(['title' => $collection->title]);
                $shopifyCollectionId = $response['id'];
                $collection->shopifyCollection()->save(new Shopify\Collection([
                    'my_collection_id' => $collection->id,
                    'shopify_collection_id' => $shopifyCollectionId,
                    'shop_id' => $this->shop->id,
                ]));
                $collection->load('shopifyCollection');
            }

            if (! $collection->pivot->shopify_collect_id) {
                try {
                    $response = $this->client->Collect->post([
                        'product_id' => $shopifyProductId,
                        'collection_id' => $collection->shopifyCollection->shopify_collection_id,
                    ]);
                    $product->collections()->updateExistingPivot($collection->id, ['shopify_collect_id' => $response['id']]);
                } catch (ApiException $e) {
                    report($e);
                }
            }
        }
    }

    public function deleteProduct(MyProduct $product): ?array
    {
        $response = $this->client->Product($product->shopifyProduct->shopify_id)->delete();

        return $response;
    }

    public function deleteProductVariant(MyProduct $myProduct, int $myProductVariantId)
    {
        $response = $this->client->Product($myProduct->shopifyProduct->shopify_id)
            ->Variant($myProductVariantId)
            ->delete()
        ;

        return $response;
    }

    public function saveVariantsToDatabase(Collection $shopifyResponse): int
    {
        if (! $shopifyResponse->get('id', false)) {
            return false;
        }
        $variants = collect($shopifyResponse->get('variants'));
        if ($variants->isEmpty()) {
            return true;
        }

        $id = $this->product->original->id;

        $insert = $variants->map(function ($variant) use ($id) {
            $productVariant = ProductVariant::where(['sku' => $variant['sku']])->first();

            return [
                'shopify_variant_id' => $variant['id'],
                'product_variant_id' => $productVariant ? $productVariant->id : null,
                'product_id' => $id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        });

        return ShopifyProductVariant::insertOnDuplicateKey($insert->toArray(), ['updated_at']);
    }

    public function prepare(MyProduct $product): array
    {
        $productData = [
            'title' => app()->isLocal() ?  date('H:i:s') . ' = ' . $product->title : $product->title,
            'body_html' => $product->description, 'published' => false
        ];

        // Images
        $productData['images'] = collect(json_decode($product->images))->prepend($product->image)->filter()->map(function ($image, $i) {
            return ['src' => url($image), 'position' => $i+1];
        })->toArray();

        // Options
        $productData['options'] = $product->options->groupBy('name')->map(function ($item, $key) {
            /** @noinspection PhpUndefinedMethodInspection */
            return ['name' => $key, 'values' => $item->pluck('value')->toArray()];
        })->values()->toArray();

        $i = 0;
        $optionKeys = [];
        foreach ($productData['options'] as $option) {
            $optionKeys[$option['name']] = 'option' . ++$i;
        }
        $optImages = collect();
        // Variants
        $productData['variants'] = $product->combinations->map(
            function ($combination) use ($optionKeys, $product, $optImages) {
                $skuOpts = collect(json_decode($combination->combination))->values();
                $sku = $combination->sku;

                if ($product instanceof Product) {
                    $options = ProductOption::whereIn('ali_sku', $skuOpts)
                        ->where('product_id', $product->id)
                        ->get()
                        ->map(function (ProductOption $option) use ($optionKeys, $optImages, $sku) {
                            if ($option->image) {
                                $optImages[$sku] = $option->image;
                            }
                            return [$optionKeys[$option->name] => $option->value];
                        })->collapse();
                } elseif ($product instanceof MyProduct) {
                    $options = MyProductOption::whereIn('ali_sku', $skuOpts)
                        ->where('my_product_id', $product->id)
                        ->get()
                        ->map(function (MyProductOption $option) use ($optionKeys, $optImages, $sku) {
                            if ($option->image) {
                                $optImages[$sku] = $option->image;
                            }
                            return [$optionKeys[$option->name] => $option->value];
                        })->collapse();
                }

                $shopifyVariantId = ShopifyProductVariant::where([
                    'product_id' => $product->id,
                    'product_variant_id' => $combination->id,
                ])->first();

                if ($shopifyVariantId) {
                    $options['id'] = $shopifyVariantId->shopify_variant_id;
                }

                $options['price'] = $combination->price;
                $options['sku'] = $combination->sku;
                $options['inventory_management'] = 'dropwow';
                $options['fulfillment_service'] = 'dropwow';
                $options['inventory_quantity'] = $combination->amount;

                return $options;
            }
        )->take(self::DEFAULT_VARIANT_BATCH_SIZE)->toArray();


        $productData['published'] = true;
        $productData['published_at'] = Carbon::now()->toIso8601String();
        $productData['published_scope'] = 'web';
        $productData['product_type'] = $product->type;
        $productData['vendor'] = $product->vendor;
        $productData['tags'] = $product->tags->map(function (MyProductTag $tag) {
            return $tag->title;
        })->implode(', ');

        return [$productData, $optImages];
    }

    /**
     * @return array|bool
     */
    public function variantsImages(Collection $shopifyResponse, Collection $optImages)
    {
        if (! $id = $shopifyResponse->get('id')) {
            return false;
        }

        $productImages = collect($shopifyResponse->get('images'))->map(function ($image) {
            return ['id' => $image['id'], 'position' => $image['position']];
        });

        $variants = collect($shopifyResponse->get('variants'));
        $images = $optImages->groupBy(function ($item) {
            return $item;
        }, true)->map->keys()->map(function (Collection $imageSku, $imageSrc) use ($variants) {
            $image = ['src' => url($imageSrc), 'variant_ids' => [], 'position' => rand(10, 100)];
            foreach ($variants as $variant) {
                if ($imageSku->search($variant['sku']) !== false) {
                    $image['variant_ids'][] = $variant['id'];
                }
            }
            if ($image['variant_ids']) {
                return $image;
            }

            return null;
        })->filter()->values()->toArray();

        // Its required to leave the old images which are already uploaded & linked to the product
        $sendImages = [];
        foreach ($productImages as $pImage) {
            $sendImages[] = $pImage;
        }
        foreach ($images as $image) {
            $sendImages[] = $image;
        }

        return $this->client->Product($id)->put(['images' => $sendImages]);
    }

    public function updateProduct(MyProduct $myProduct): int
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        [$productData, $optImages] = $this->prepare($myProduct);

        $response = collect($this->client->Product($myProduct->shopifyProduct->shopify_id)->put($productData));

        unset($productData['body_html']);

        return $response->get('id');
    }

    public function syncOrders(?int $orderId = null, bool $force = false): array
    {
        try {
            $params = ['status' => 'any'];
            if ($orderId) {
                $params['ids'] = $orderId;
            }

            $orders = $this->client->Order->get($params);
        } catch (ApiException $e) {
            return [['status' => 500, 'data' => 'ApiError ' . $e->getMessage() ]];
        }

        $return = [];
        foreach ($orders as $order) {
            $return[$order['id']] = ShopifyOrder::store($this->shop, collect($order), $force);
        }

        return $return;
    }
}
