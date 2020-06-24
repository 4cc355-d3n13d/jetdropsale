<?php
namespace App\Permissions\Roles;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderCart;
use App\Models\Product\Product;
use App\Models\Shopify\Shop;
use App\Models\User;
use App\Nova\MyProduct;
use OwenIt\Auditing\Models\Audit;

class ManagerRole extends Role
{
    public function __construct()
    {
        parent::__construct($name = "Manager");
    }

    public function init()
    {
        $this->add([
            'viewNova',
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
            ['update' => Order::class],

            ['viewAny'=> Invoice::class],
            ['view'   => Invoice::class],
            ['update' => Invoice::class],

            ['viewAny'=> User::class],
            ['view'   => User::class],
            ['update' => User::class],
            ['create' => User::class],

            ['viewAny'=> Shop::class],
            ['view'   => Shop::class],
            ['update' => Shop::class],
            ['create' => Shop::class],
        ]);
    }
}
