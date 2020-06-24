<?php /** @noinspection PhpUndefinedFieldInspection */
declare(strict_types=1);

namespace App\Services;

use App\Models\Shopify\Shop;
use App\Models\User;
use App\Models\User\SocialAccount;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\User as SocialUser;

/**
 * Class SocialAccountService
 */
class SocialAccountService extends ServiceProvider
{
    /**
     * @param SocialUser $providerUser
     * @param string     $providerName
     */
    public static function createOrGetUser(SocialUser $providerUser, string $providerName): User
    {
        if (! $account = SocialAccount::where([
            'provider' => $providerName,
            'provider_user_id' => $providerUser->getId()
        ])->first()) {
            $account = new SocialAccount([
                'provider_user_id' => $providerUser->getId(),
                'provider' => $providerName,
            ]);

            if (! $user = User::where('email', $providerUser->getEmail())->first()) {
                $user = User::createBySocialProvider($providerUser, $providerName);
            }

            $account->user()->associate($user);
        }

        if ('shopify' === $providerName) {
            $shop = Shop::firstOrNew([
                'shop' => $providerUser->user['myshopify_domain'],
                'user_id' => $account->user->id
            ], ['status' => 1]);

            $shop->access_token = $providerUser->accessTokenResponseBody['access_token'];
            $shop->save();
            event('shopify.init-hooks', $shop);
        }

        $account->provider_user_data = json_encode($providerUser->user, JSON_UNESCAPED_UNICODE);
        $account->save();

        return $account->user;
    }
}
