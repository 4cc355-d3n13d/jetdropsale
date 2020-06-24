<?php

namespace App\Console\Commands;

use App\Enums\MyProductStatusType;
use App\Models\Product\MyProduct;
use App\Models\Shopify\ShopifyProduct;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class GarbageCollector extends Command
{
    public const INTERVAL_MIN = 10;
    private const BATCH_SIZE = 100;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'gc';

    /**
     * The console command description.
     */
    protected $description = 'Command description';


    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->fixProductsBeenExportedToShopify();
    }

    protected function fixProductsBeenExportedToShopify(): void
    {
        ShopifyProduct
            ::whereNull('shopify_id')
            ->where('updated_at', '<', now()->subMinute(self::INTERVAL_MIN))
            ->chunk(self::BATCH_SIZE, function (Collection $shopifyProducts) {
                MyProduct::whereIn('id', $shopifyProducts->pluck('my_product_id'))->update(['status' => MyProductStatusType::NON_CONNECTED]);
                ShopifyProduct::whereIn('id', $shopifyProducts->pluck('id'))->delete();
            })
        ;
    }
}
