<?php

namespace App\Http\Controllers\Api\External\Shopify;

use App\Http\Controllers\Api\External\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StockUpdateController extends ApiController
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop' => [
                'required',
                'string',
                'exists:shopify_shops',
            ],
            'sku' => 'string'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed when for shopify fetch stock. '. $request->getRequestUri(), $validator->errors()->messages());
            return $validator->errors();
        }

        //get stock
        $stock=[];
        $extsku = $request->get('sku');

        $shop = $request->get('shop');

        if ($extsku) {
            $stock =  DB::table('my_product_variants')
                    ->join('my_products', 'my_products.id', '=', 'my_product_variants.my_product_id')
                    ->join('shopify_shops', 'shopify_shops.user_id', '=', 'my_products.user_id')
                    ->select('my_product_variants.sku', 'my_product_variants.amount')
                    ->where('shopify_shops.shop', $shop)
                    ->where('my_product_variants.sku', $extsku)
                    ->get()
                    ->pluck('amount', 'sku')->all()
            ;
        } else {
            $stock =
                DB::table('my_product_variants')
                    ->join('my_products', 'my_products.id', '=', 'my_product_variants.my_product_id')
                    ->join('shopify_shops', 'shopify_shops.user_id', '=', 'my_products.user_id')
                    ->select('my_product_variants.sku', 'my_product_variants.amount')
                    ->where('shopify_shops.shop', $shop)
                    ->get()
                    ->pluck('amount', 'sku')->all()
            ;
        }

        Log::info('Fetch stock: Successfully return stock count.');

        return $stock;
    }
}
