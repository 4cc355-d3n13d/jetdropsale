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

class ViewFeaturesRole extends Role
{
    public function __construct()
    {
        parent::__construct($name = "ViewFeatures");
    }

    public function init()
    {
        $this->add('viewFeatures');
    }
}
