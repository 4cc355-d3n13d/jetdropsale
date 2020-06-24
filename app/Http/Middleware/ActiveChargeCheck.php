<?php

namespace App\Http\Middleware;

use App\Models\Shopify\Charge;
use App\Models\User;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Auth;

class ActiveChargeCheck extends Authenticate
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string[] ...$guards
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {

        /** @var User $user */
        $user = Auth::user();

        if (!$user || !$user->shops()->exists()) {
            return $next($request);
        }

        if (!Charge::where(
            [
                'status' => 'active',
                'shop_id' => $user->shops()->first()->id,
            ]
        )->first()) {
            return redirect(
                url('/logout')
            );
        }

        return $next($request);
    }
}
