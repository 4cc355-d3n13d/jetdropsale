<?php

namespace Tests\Feature\Api\External;

use App\Enums\MyProductStatusType;
use App\Models\Product\MyProduct;
use App\Models\Shopify\Shop;
use App\Models\Shopify\ShopifyProduct;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\ApiTestCase;
use Tests\Traits;
use App\Models\Shopify;

class ShopifyDisconnectMyProductTest extends ApiTestCase
{
    use DatabaseMigrations;
    use Traits\ArrangeThings;
    use Traits\UsesSqlite;

    public const SHOPIFY_ID = 1568115490910;

    public function testMyProductDelete(): void
    {
        $this
            ->arrangeUserShop()
        ;

        $shop = Shopify\Shop::lastOrFail();

        $myProductConnected1 = create(MyProduct::class, [
            'user_id' => auth()->id(),
            'status' => MyProductStatusType::CONNECTED,
        ]);

        $shopifyProduct = create(ShopifyProduct::class, [
            'shopify_id' => self::SHOPIFY_ID,
            'user_id' => $shop->user_id,
            'my_product_id' => $myProductConnected1->id,
        ]);

        $myProductConnected1->shopify_product_id = $shopifyProduct->id;
        $myProductConnected1->save();

        $this->seeInDatabase('shopify_products', [
            'shopify_id' => self::SHOPIFY_ID,
            'my_product_id' => $myProductConnected1->id,
        ]);

        $this->notSeeInDatabase('my_products', [
            'id' => $myProductConnected1->id,
            'status' => MyProductStatusType::NON_CONNECTED
        ]);

        $this->postJson('/test/shopify/products/delete', ['id' => self::SHOPIFY_ID], [
            'x-shopify-hmac-sha256' => 1,
            'x-shopify-product-id' => self::SHOPIFY_ID,
            'x-shopify-topic' => 'products/delete'
        ])->seeStatusCode(200);

        $this->seeInDatabase('my_products', [
            'id' => $myProductConnected1->id,
            'status' => MyProductStatusType::NON_CONNECTED,
            'shopify_product_id' => null,
        ]);

        $this->notSeeInDatabase('my_products', [
            'id' => $myProductConnected1->id,
            'status' => MyProductStatusType::CONNECTED
        ]);
    }
}
