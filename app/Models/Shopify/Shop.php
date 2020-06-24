<?php

namespace App\Models\Shopify;

use App\Jobs\MarketSync\ImportProductFromCSCart;
use App\Models\User;
use App\Services\CSCartClientService;
use App\Services\ShopifyService;
use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\Shopify\Shop
 *
 * @property int $id
 * @property int $user_id
 * @property string $shop
 * @property string $access_token
 * @property int $status
 * @property bool $imported
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $user
 * @mixin \Eloquent
 */
class Shop extends Model
{
    protected $table = 'shopify_shops';

    protected $fillable = [
        'user_id',
        'shop',
        'access_token',
        'status',
        'imported'
    ];

    protected $dispatchesEvents = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAccessToken(): string
    {
        return $this->access_token;
    }

    public function syncCsCart(self $shop = null)
    {
        if (is_null($shop)) {
            $shop = $this;
        }
        // When success summons ImportOrdersJob
        ImportProductFromCSCart::dispatch($shop);
    }

    public function isExistsInCSCart(): bool
    {
        $client = app()->make(CSCartClientService::class);

        try {
            $response =  $client->get(env('CSCART_API_MIGRATION_ENDPOINT') . '?shop=' . $this->shop);

            return (bool) (isset(json_decode($response, true)['data']['user']));
        } catch (\Http\Client\Exception $e) {
            Log::error('Error accessing the CSCart API', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * @return ShopifyService
     */
    public function shopify()
    {
        $client = new ShopifyService();
        $client->setClient($this);
        return $client;
    }
}
