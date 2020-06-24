<?php

namespace App\Http\Controllers\Api\External\Shopify;

use App\Enums\MyProductStatusType;
use App\Http\Controllers\Api\External\ApiController;
use App\Models\Shopify\Shop;
use App\Models\Shopify\ShopifyProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductsController extends ApiController
{
    public function delete(Request $request)
    {
        if ($request->header('x-shopify-topic') !== 'products/delete') {
            return ['status' => Response::HTTP_OK, 'data' => 'wrong request topic'];
        }

        $shopifyProduct = ShopifyProduct::where(
            [
                'shopify_id' => $request->header('x-shopify-product-id')
            ]
        )->get()->first();

        if (!$shopifyProduct) {
            return ['status' => Response::HTTP_OK, 'data' => 'shopify product not found'];
        }

        $myProduct = $shopifyProduct->myProduct;

        if (!$myProduct) {
            return ['status' => Response::HTTP_OK, 'data' => 'my product not found'];
        }

        $realShop = Shop::where([
            'user' => $myProduct->user,
            'shop' => $request->header('x-shopify-shop-domain')
        ]);

        if (!$realShop) {
            return ['status' => Response::HTTP_OK, 'data' => 'shop not found'];
        }

        $myProduct->status = MyProductStatusType::NON_CONNECTED;
        $myProduct->shopify_product_id = null;
        $myProduct->save();

        $shopifyProduct->delete();

        return ['status' => Response::HTTP_OK, 'data' => 'Product deleted'];
    }
}
