<?php
namespace App\Permissions\Roles;

use App\Models\Card;
use App\Models\Order;
use App\Models\Product\MyProduct;
use App\Models\Product\MyProductCollection;
use App\Models\Product\MyProductTag;
use App\Models\User\Setting;
use App\Permissions\Rules\OwnerRule;

class OwnerRole extends Role
{
    public function __construct()
    {
        parent::__construct('Owner');
    }

    protected function init()
    {
        // The list of models which foreign version cannot be shown to another user
        $this
            ->add(new OwnerRule('*', Order::class))
            ->add(new OwnerRule('*', Card::class))
            ->add(new OwnerRule('*', Setting::class))
            ->add(new OwnerRule('*', MyProduct::class))
            ->add(new OwnerRule('*', MyProductTag::class))
            ->add(new OwnerRule('*', MyProductCollection::class))
        ;
    }
}
