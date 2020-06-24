<?php

namespace App\Console\Commands;

use App\Enums\MyProductStatusType;
use App\Models\Product\MyProduct;
use App\Models\Shopify\ProductVariant;
use App\Models\Shopify\Shop;
use App\Models\Shopify\ShopifyProduct;
use Illuminate\Console\Command;

class ShopifyProductsSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopify:products-sync {shop_id} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаляем все продукты в шопифае, о которых мы ничего не знаем';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $shop = Shop::query()->findOrFail($this->argument('shop_id'));
        $this->info(
            "Вначале переведем все статусы в NonConnected" .
             MyProduct::where('user_id', $shop->user_id)->where('status', MyProductStatusType::CONNECTED)->update(['status'=>MyProductStatusType::NON_CONNECTED])
        );
        $page = 1;
        $this->info("Начинаем работать");
        while ($products = $shop->shopify()->getClient()->Product->get(['limit' => 100, 'page'=>$page])) {
            collect($products)
                ->each(function ($shopifyProduct) use ($shop) {
                    if ($sp = ShopifyProduct::where('shopify_id', $shopifyProduct['id'])->first()) {
                        tap($sp->myProduct, function (MyProduct $myProduct) use ($shop) {
                            $myProduct->status = MyProductStatusType::CONNECTED;
                            $myProduct->save();
                        });
                        $countVariants = count($shopifyProduct['variants']);
                        $this->info("Found product! {$shopifyProduct['id']} ($countVariants)");
                        foreach ($shopifyProduct['variants'] as $variant) {
                            if (ProductVariant::where('shopify_variant_id', $variant['id'])->doesntExist()) {
                                $this->info("Variant not found. Deleting! " . $variant['id']);
                                $shop->shopify()->getClient()->ProductVariant($variant['id'])->delete();
                                $countVariants--;
                            }
                        }
                        if ($countVariants == 0) {
                            $this->info("No variants. Reexport  product ");
                            $shop->shopify()->getClient()->Product($shopifyProduct['id'])->delete();
                            tap($sp->myProduct, function (MyProduct $myProduct) use ($shop) {
                                $myProduct->status = MyProductStatusType::NON_CONNECTED;
                                $myProduct->save();
                                // $myProduct->exportToShopify($shop); // Does not work :(
                            });
                            $sp->delete();
                        }
                    } else {
                        $this->info("Product not found! Deleting {$shopifyProduct['id']}");
                        $shop->shopify()->getClient()->Product($shopifyProduct['id'])->delete();
                    }
                });
            $page++;
        }
        $this->info("Stop. Pages = " . $page);
    }
}
