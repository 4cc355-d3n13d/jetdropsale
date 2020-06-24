<?php

namespace App\Listeners\Order;

use App\Models\OrderCart;

class OrderRecalcSum extends AbstractOrderListener
{
    public function handle(OrderCart $cart)
    {
        if (!$cart->order_id) {
            return;
        }
        $order = $cart->order;

        $price = $order->price;
        $order->reCalcPrice();
        if ($price != $order->price) {
            $order->save();
            if ($order->invoice) {
                $order->invoice->calcTotalSum();
                $order->invoice->save();
            }
        }
    }
}
