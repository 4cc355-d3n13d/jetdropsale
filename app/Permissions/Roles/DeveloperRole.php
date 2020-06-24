<?php

namespace App\Permissions\Roles;

use App\Models\Audit;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderCart;
use App\Models\Product\MyProduct;
use App\Models\Product\Product;
use App\Models\Setting;
use App\Models\Shopify\Shop;
use App\Models\User;

class DeveloperRole extends Role
{
    public function __construct()
    {
        parent::__construct("Developer");
    }

    protected function init()
    {
        $this->add([
            'viewNova',
            'viewNovaDevTools',
            'viewAliLink',
            'viewFeatures',
            'viewShopifyData',

            ['*' => Product::class],
            ['*' => MyProduct::class],
            ['*' => OrderCart::class],


            ['viewAny' => Audit::class],
            ['view' => Audit::class],


            ['viewAny'=> Order::class],
            ['view'   => Order::class],

            ['viewAny'=> Invoice::class],
            ['view'   => Invoice::class],

            ['viewAny'=> User::class],
            ['view'   => User::class],

            ['viewAny'=> Shop::class],
            ['view'   => Shop::class],

            ['viewAny' => Setting::class],
            ['view' => Setting::class],
            ['edit' => Setting::class],
            ['create' => Setting::class],


        ]);
    }
}
