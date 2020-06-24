<?php

namespace App\Jobs\MarketSync;

use App\Enums\MyProductStatusType;
use App\Enums\ShopifyProductStatusType;
use App\Models\Product;
use App\Models\Shopify\Shop;
use App\Models\Shopify\ShopifyProduct;
use App\Services\CSCartClientService;
use App\Services\MyProductService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ImportProductFromCSCart implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var MyProductService */
    public $myProductService;

    /** @var Shop */
    private $shop;

    /** @var \stdClass */
    private $response;


    public function __construct(Shop $shop)
    {
        $this->shop = $shop;
    }

    public function handle(MyProductService $myProductService): void
    {
        if (! $this->shop->user) {
            Log::error('Cannot get user for this shop');
            return;
        }

        if ($responseJson = $this->getApiResponse()) {
            try {
                $this->response = json_decode($responseJson);
                $this->addProducts($myProductService);
                ImportOrders::dispatch($this->shop, $this->response);
            } catch (\Exception $e) {
                Log::error('Cannot synchronize data for this shop', ['error' => $e->getMessage()]);
            }
            $this->shop->imported = true;
            $this->shop->save();
        }
    }

    private function addProducts(MyProductService $myProductService): void
    {
        foreach ($this->response->data->products as $shopifyId => $aliId) {
            if (! $product = Product\Product::where('ali_id', $aliId)->first()) {
                Log::error('Can not find the product with this ali ID', ['ali-id' => $aliId]);
                continue;
            };

            $myProduct = Product\MyProduct::where(['product_id'=>$product->id, 'user_id'=>$this->shop->user->id])->first();
            if (!$myProduct) {
                $myProduct = $myProductService->clone($product, $this->shop->user);
            }


            $myProduct->status = MyProductStatusType::CONNECTED;

            $shopifyProductValidator = Validator::make(
                [
                    'shopify_id' => $shopifyId,
                    'user_id' => $this->shop->user->id,
                ],
                [
                    'user_id' => Rule::unique('shopify_products')->where(function ($query) use ($shopifyId) {
                        $query->where('shopify_id', $shopifyId);
                    })
                ]
            );

            if ($shopifyProductValidator->fails()) {
                Log::error('Shopify product already exists', [
                    'user_id' => $this->shop->user->id,
                    'shopify_id' => $aliId,
                ]);
                continue;
            }



            try {
                $shopifyProduct = ShopifyProduct::firstOrCreate([
                    'shopify_id' => $shopifyId,
                    'my_product_id' => $myProduct->id,
                    'combination_id' => null,
                    'user_id' => $this->shop->user->id,
                    'status' => ShopifyProductStatusType::OK
                ]);
            } catch (QueryException $e) {
                Log::error('Can\'t save shopify product ', ['ali-id' => $aliId]);
                continue;
            }

            $myProduct->shopify_product_id = $shopifyProduct->id;
            $myProduct->save();
        }
    }

    private function getApiResponse(): ?string
    {
        /** @var CSCartClientService $client */
        $client = app()->make(CSCartClientService::class);

        try {
            return $client->get(env('CSCART_API_MIGRATION_ENDPOINT') . '?shop=' . $this->shop->shop);
        } catch (\Http\Client\Exception $e) {
            Log::error('Error accessing the API', ['error' => $e->getMessage()]);

            return null;
        }
    }
}
