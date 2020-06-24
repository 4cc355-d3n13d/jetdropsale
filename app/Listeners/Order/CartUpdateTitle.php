<?php

namespace App\Listeners\Order;

use App\Models\OrderCart;

class CartUpdateTitle extends AbstractOrderListener
{
    public function handle(OrderCart $cart)
    {
        if ($cart->isDirty(['goods_type', 'goods_id'])) {
            if ($cart->goods_type) {
                if ($goods = $cart->goods_id ? $cart->load('goods')->goods : app($cart->goods_type)) {
                    $cart->title = $goods->getTitle() ?? $cart->title;
                    $cart->image = $goods->getImage() ?? $cart->image;
                    $cart->price = $goods->getPrice() ?? $cart->price;
                }
            }
        }
    }
}
