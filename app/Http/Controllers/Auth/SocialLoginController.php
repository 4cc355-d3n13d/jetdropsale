<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\WebController;
use App\Models\Shopify\Charge;
use App\Models\Shopify\Shop;
use App\Services\ShopifyService;
use App\Services\SocialAccountService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Laravel\Socialite\Contracts\Factory as Socialite;
use PHPShopify\Exception\ApiException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class SocialLoginController
 */
class SocialLoginController extends WebController
{
    protected const LATEST_URL = 'latest_url';

    private $socialite;

    /**
     * SocialLoginController constructor.
     *
     * @param Socialite $socialite
     */
    public function __construct(Socialite $socialite)
    {
        $this->socialite = $socialite;
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * @param string $provider
     */
    public function login(string $provider): RedirectResponse
    {
        $social = $this->socialite->driver($provider);
        $shop = request()->get('shop', false);
        if ('shopify' === $provider && $shop) {
            $shop = parse_url($shop)['host'] ?? $shop;
            $shop = preg_replace(['|https?://|', '|\.myshopify\.com.*|', '|/|', '|\.$|'], '', $shop);
            request()->request->set('shop', $shop . '.myshopify.com');
            $social->scopes([
                'read_orders',
                'write_orders',
                'read_products',
                'write_products',
                'read_script_tags',
                'write_script_tags',
                'write_shipping',
                'read_shipping',
                'read_inventory',
                'write_inventory',
                'read_locations',
                'write_fulfillments'
            ]);
            return $social->redirect();
        }
        return back();
    }

    /**
     * Log the user out of the application.
     **
     * @throws \RuntimeException
     */
    public function logout(): RedirectResponse
    {
        {
            auth()->guard()->logout();

            request()->session()->flush();
            request()->session()->regenerate();
        }

        return redirect()->back();
    }

    /**
     * @param string $provider
     * @param Request $request
     */
    public function callback(string $provider, Request $request): RedirectResponse
    {
        try {
            $socialiteUser = $this->socialite->driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            throw new HttpException($e->getCode(), $e->getMessage());
        }

        $user = SocialAccountService::createOrGetUser($socialiteUser, $provider);

        if ($user && $user->shops) {
            auth()->login($user, true);

            /** @var Shop $shop */
            $shop = $user->shops->first();

            /** @var ShopifyService $shopify */
            $shopify = app(ShopifyService::class);
            $shopify->setClient($shop);

            $fulfillment_services = $shopify->getClient()->FulfillmentService()->get();

            //check if there dropwow handle service
            if ((array_search('dropwow', array_column($fulfillment_services, 'handle'))) === false) {
                try {
                    $shopify->getClient()->FulfillmentService()->post([
                    'name' => 'Dropwow',
                    'callback_url' => route('shopify_callback.'),
                    'inventory_management' => true,
                    'tracking_support' => true,
                    'requires_shipping_method' => true,
                    'format' => 'json'
                    ]);
                } catch (\Exception $e) {
                    // Sometimes we have an exception here. Should be fixed later
                }
            }

            if (Charge::where([
                'status' => 'active',
                'shop_id' => $shop->id,
            ])->first()) {
                return redirect('/');
            }

            if ($shop->isExistsInCSCart()) {
                Charge::createEarlyAceessFreeCharge($shop);
                return redirect('/');
            }

            $shopifyRequestFields = [
                'name' => env('SHOPIFY_CHARGE_NAME'),
                'price' => env('SHOPIFY_CHARGE_PRICE'),
                'return_url' => route('checkCharge'),
                'test' => strpos(auth()->user()->email, '@dropwow.com') ? true : env('SHOPIFY_CHARGE_TEST_CHARGES'),
                'trial_days' => env('SHOPIFY_CHARGE_TRIAL_DAYS'),
            ];

            try {
                $recurrentChargeEndpoint = $shopify->getClient()->RecurringApplicationCharge;
                $recurrentCharge = $recurrentChargeEndpoint->post($shopifyRequestFields);
            } catch (ApiException $e) {
                $client = $shopify->getClient();

                report($e);
                Log::channel('shopify')->error('Charge create error', [
                    'e' => $e->getMessage(),
                    'config' => $client::$config,
                    'url' => $recurrentChargeEndpoint->generateUrl(),
                    'request_fields' => $shopifyRequestFields,
                ]);

                return redirect('/');
            }

            return redirect($recurrentCharge['confirmation_url']);
        }

        return redirect(route('register'));
    }
}
