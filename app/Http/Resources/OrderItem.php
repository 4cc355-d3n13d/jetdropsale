<?php

namespace App\Http\Resources;

use App\Enums\OrderOriginType;
use App\Enums\OrderStatusType;
use App\Models\Order;
use App\Models\ShipGoods;

class OrderItem extends Item
{
    public function toArray($request)
    {
        /** @var Order $this */
        $canBe = OrderStatusType::getAvailableManualTransitions($this->status);
        foreach ($canBe as &$status) {
            /** @var Order $this */
            $status = route('user.order.status', ['order_id' => $this->id, 'new_status' => $status]);
        }
        unset($status);

        /** @var Order $this */
        return [
            'id'                => (int) $this->id,
            'origin'            => OrderOriginType::getKey($this->origin),
            'origin_id'         => (int) $this->origin_id,
            'origin_path'       => $this->shop ? "https://{$this->shop->shop}/admin/orders/{$this->origin_id}" : "",
            'cart'              => $this->cart->map->only(['id', 'title', 'image', 'price', 'quantity']),
            'price'             => (float) $this->price,
            'shipping_address'  => is_string($this->shipping_address) ? nl2br($this->shipping_address) : $this->shipping_address,
            'shipping_price'    => resolve(ShipGoods::class)->price,
            'tracking_number'   => $this->tracking_number,
            'status'            => OrderStatusType::getKey($this->status),
            'created_at'        => $this->created_at->format('Y-m-d H:i:s'),
            'auto_confirm_secs' => $this->auto_confirm_at ? now()->diffInRealSeconds($this->auto_confirm_at) : null,
            'can_be'            => $canBe,
        ];
    }
}
