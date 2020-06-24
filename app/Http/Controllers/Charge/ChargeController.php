<?php declare(strict_types=1);

namespace App\Http\Controllers\Charge;

use App\Http\Controllers\WebController;
use App\Models\Shopify\Charge;
use App\Models\Shopify\Shop;
use App\Services\ShopifyService;
use Illuminate\Http\Request;

class ChargeController extends WebController
{
    public function checkChargeAction(Request $request)
    {
        if ($request->has('charge_id')) {
            $user = auth()->user();
            /** @var Shop $shop */
            $shop = $user->shops->first();
            /** @var ShopifyService $shopify */
            $shopify = app(ShopifyService::class);
            $shopify->setClient($shop);

            $charge = $shopify->getClient()->RecurringApplicationCharge($request->get('charge_id'))->get();

            if ($charge['status'] == 'accepted') {
                $activateResult = $shopify->getClient()->RecurringApplicationCharge($request->get('charge_id'))->activate();

                Charge::create(array_merge([
                        'charge_id' => $activateResult['id'],
                        'shop_id' => $shop->id,
                        'name' => env('SHOPIFY_CHARGE_NAME'),
                        'price' => env('SHOPIFY_CHARGE_PRICE'),
                ], $activateResult));

                return redirect('/');
            }
        }
    }
}
