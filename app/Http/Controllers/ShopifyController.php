<?php

namespace App\Http\Controllers;

use App\Models\Shopify\Shop;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class ShopifyController extends WebController
{
    public function orders()
    {
        $shopify =  \App\Models\Shopify\Shop::where('shop', request('shop'))->firstOrFail()->shopify()
                ->getClient();
            

        return view(
            'data.shopify_orders',
                [
                'data' => request('ids') ?
                array_values($shopify->Order->get(['ids'=> request('ids'), 'status'=>'any'])) :
                $shopify->Order->get(['status'=>'any']),
                ]
        );
    }

    public function orderSync()
    {
        $shop = Shop::where('shop', request('shop'))->firstOrFail();
        $output = new BufferedOutput;
        $data = collect()
                ->push(['shop_id'=>$shop->id])
                ->push(['order_id'=>request('id')])
                ->when(request()->has('force'), function ($data) {
                    return $data->push(['--force' => 1]);
                })->collapse();
        Artisan::call('shopify:orders-sync', $data->toArray(), $output);
        dd($output->fetch());
    }
}
