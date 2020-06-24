<?php

namespace Tests\Feature;

use App\Enums\MyProductStatusType;
use App\Models\Product\MyProduct;
use App\Models\Shopify\Shop;
use App\Services\ShopifyService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Stubs\FakeShopifyService;
use Tests\TestCase;
use Tests\Traits\UsesSqlite;

class SendClonedProductToShopifyTest extends TestCase
{
    use DatabaseMigrations, UsesSqlite;

    public function testSendExistentProductToShopify(): void
    {
        $this->app->bind(ShopifyService::class, FakeShopifyService::class);

        $this->signIn();
        $shop = create(Shop::class, ['user_id' => auth()->id()]);
        $myProduct = create(MyProduct::class, ['user_id' => $shop->user_id]);

        $this
            ->postJson('/api/my-products/shopify/send', ['ids' => [$myProduct->id]])
            ->assertResponseStatus(200)
            ->seeJson(['result' => 'ok'])
            ->seeJsonStructure([
                'my_products' => [
                    $myProduct->id => ['result']
                ]
            ])
        ;

        $this->seeInDatabase('my_products', [
            'id' => $myProduct->id,
            'shopify_product_id' => $myProduct->id,
            'status' => env('QUEUE_DRIVER') === 'sync'
                ? MyProductStatusType::CONNECTED
                : MyProductStatusType::SHOPIFY_SEND_PENDING,
        ]);
    }
}
