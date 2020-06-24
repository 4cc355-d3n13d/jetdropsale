<?php

namespace App\Jobs\Shopify;

use App\Models\Shopify\Shop;
use App\Services\ShopifyService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use PHPShopify\Exception\ApiException;

class InitShopifyHooks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $shop;

    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
        $this->queue = 'high';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!app()->runningUnitTests()) {
            $shopify = app(ShopifyService::class);
            $shopify->setClient($this->shop);

            $domain = 'https://' . env('API_HOST') .  '/shopify';

            // shopify webhooks - https://help.shopify.com/en/api/reference/events/webhook
            collect([
                ['topic' => 'app/uninstalled'],

                ['topic' => 'carts/create'],
                ['topic' => 'carts/update'],

                ['topic' => 'checkouts/create'],
                ['topic' => 'checkouts/update'],

                ['topic' => 'collections/create'],
                ['topic' => 'collections/update'],
                ['topic' => 'collections/delete'],

                ['topic' => 'collection_listings/add'],
                ['topic' => 'collection_listings/remove'],
                ['topic' => 'collection_listings/update'],

                ['topic' => 'customers/create'],
                ['topic' => 'customers/disable'],
                ['topic' => 'customers/enable'],
                ['topic' => 'customers/update'],
                ['topic' => 'customers/delete'],

                ['topic' => 'customer_groups/create'],
                ['topic' => 'customer_groups/update'],
                ['topic' => 'customer_groups/delete'],

                ['topic' => 'draft_orders/create'],
                ['topic' => 'draft_orders/update'],
                ['topic' => 'draft_orders/delete'],

                ['topic' => 'fulfillments/create'],
                ['topic' => 'fulfillments/update'],

                ['topic' => 'fulfillment_events/create'],
                ['topic' => 'fulfillment_events/delete'],

                ['topic' => 'inventory_items/create'],
                ['topic' => 'inventory_items/update'],
                ['topic' => 'inventory_items/delete'],

                ['topic' => 'inventory_levels/connect'],
                ['topic' => 'inventory_levels/update'],
                ['topic' => 'inventory_levels/disconnect'],

                ['topic' => 'locations/create'],
                ['topic' => 'locations/update'],
                ['topic' => 'locations/delete'],

                ['topic' => 'orders/cancelled'],
                ['topic' => 'orders/create'],
                ['topic' => 'orders/fulfilled'],
                ['topic' => 'orders/paid'],
                ['topic' => 'orders/partially_fulfilled'],
                ['topic' => 'orders/updated'],
                ['topic' => 'orders/delete'],

                ['topic' => 'order_transactions/create'],

                ['topic' => 'products/create'],
                ['topic' => 'products/update'],
                ['topic' => 'products/delete'],

                ['topic' => 'product_listings/add'],
                ['topic' => 'product_listings/remove'],
                ['topic' => 'product_listings/update'],

                ['topic' => 'refunds/create'],

                ['topic' => 'shop/update'],

                ['topic' => 'themes/create'],
                ['topic' => 'themes/publish'],
                ['topic' => 'themes/update'],
                ['topic' => 'themes/delete'],

            ])->each(function ($hook) use ($shopify, $domain) {
                $hook['format'] = 'json';
                $hook['address'] = $domain . '/' . $hook['topic'];
                try {
                    $shopify->webhook($hook);
                } catch (ApiException $e) {
                }
            });
        }
    }
}
