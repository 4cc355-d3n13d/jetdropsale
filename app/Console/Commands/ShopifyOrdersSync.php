<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Shopify\Shop;
use Illuminate\Console\Command;

class ShopifyOrdersSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopify:orders-sync {shop_id?} {order_id?} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync orders in shopify';


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
        Shop::query()->when($this->argument('shop_id'), function ($query) {
            $query->find($this->argument('shop_id'));
        })->latest()->get()->each(function (Shop $shop) {
            $this->info('Shop['. $shop->id .'] = ' . $shop->shop);
            foreach ($shop->shopify()->syncOrders($this->argument('order_id'), $this->option('force')) as $key => $value) {
                $this->info($key . ' = (' . $value['status'] . ') ' .$value['data']);
            }
            $this->info('=======================');
        });
    }
}
