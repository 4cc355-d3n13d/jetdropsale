<?php

namespace App\Http\Controllers\Api\External\Shopify;

use App\Http\Controllers\Api\External\ApiController;
use App\Models\Shopify;
use App\Models\Shopify\Shop;
use App\Services\ShopifyOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class OrderController
 */
class OrderController extends ApiController
{
    public function create(Request $request): array
    {
        $shop = $request->header('x-shopify-shop-domain');
        $hook = collect(json_decode($request->getContent()));

        if (! $hook->get('id', false)) {
            return ['status' => Response::HTTP_NOT_IMPLEMENTED, 'data' => 'order.id not defined'];
        }

        if (! $shop = Shopify\Shop::where('shop', $shop)->first()) {
            return ['status' => Response::HTTP_OK, 'data' => 'shop not found'];
        }

        return ShopifyOrder::store($shop, $hook);
    }
}
