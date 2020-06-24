<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Class ShopifyAuth
 */
class ShopifyAuth
{
    /**
     * Handle an incoming request.
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $hmac = $request->header('x-shopify-hmac-sha256') ?: '';
        $shop = $request->header('x-shopify-shop-domain');
        $data = $request->getContent();

        $secret = config('services.shopify.client_secret');
        // From https://help.shopify.com/api/getting-started/webhooks#verify-webhook
        $hmacLocal = base64_encode(hash_hmac('sha256', $data, $secret, true));
        // dd($hmac, $shop, $secret, $hmacLocal, $data);
        if (!in_array(env('APP_ENV'), ['local', 'testing']) && (!hash_equals($hmac, $hmacLocal) || empty($shop))) {
            // Issue with HMAC or missing shop header
            Log::channel('shopify')->error('No AUTH', [
                'hmac' => $hmac,
                'hmacLocal' => $hmacLocal,
                'shop' => $shop,
                'secret' => $secret
            ]);
            return response(json_encode(['status' => Response::HTTP_NOT_IMPLEMENTED, 'data' => 'no auth']), 200);
        }

        // All good, process webhook
        return $next($request);
    }
}
