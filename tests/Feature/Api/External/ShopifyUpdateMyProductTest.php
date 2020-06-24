<?php

namespace Tests\Feature\Api\External;

use App\Jobs\MarketSync\SyncRemovedMyProductVariantsToShopify;
use App\Models\Product\MyProduct;
use App\Models\Product\MyProductOption;
use App\Models\Product\MyProductVariant;
use App\Models\Product\Product;
use App\Models\Product\ProductOption;
use App\Models\Product\ProductVariant;
use App\Models\Shopify\ProductVariant as ShopifyProductVariant;
use App\Models\Shopify\Shop;
use App\Models\Shopify\ShopifyProduct;
use App\Services\ShopifyService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Bus;
use Storage;
use Tests\Stubs\FakeShopifyService;
use Tests\TestCase;
use Tests\Traits\ArrangeThings;
use Tests\Traits\UsesSqlite;

class ShopifyUpdateMyProductTest extends TestCase
{
    use DatabaseMigrations, UsesSqlite;
    use ArrangeThings;
    private const SOURCE_REFERENCE = 'src_1D9EaTIJb8S3vLIy3zMX0I4y';
    private const CUSTOMER_REFERENCE = 'cus_DaPPcfkr32xm3T';

    public function testUpdateOfUserEditSentToShopify(): void
    {
        app()->bind(ShopifyService::class, FakeShopifyService::class);
        $this->signIn();
        create(Shop::class, ['shop' => 'test_shop.myshopify.com', 'user_id' => auth()->id()]);

        /** @var MyProduct $myProduct */
        $product = create(Product::class);
        $product->options()->save(factory(ProductOption::class)->make());
        $product->combinations()->save(factory(ProductVariant::class)->make());


        $myProduct = create(MyProduct::class, ['user_id' => auth()->id(), 'product_id' => $product->id]);

        $shopifyProduct = create(ShopifyProduct::class, [
            'shopify_id' => 1,
            'user_id' => auth()->id(),
            'my_product_id' => $myProduct->id,
        ]);

        $myProduct->options()->save(factory(MyProductOption::class)->make());
        $myProduct->combinations()->save(factory(MyProductVariant::class)->make());
        $myProduct->options()->save(factory(MyProductOption::class)->make());
        $myProduct->combinations()->save(factory(MyProductVariant::class)->make());

        $myProduct->combinations()->each(function (MyProductVariant $myProductVariant) {
            create(ShopifyProductVariant::class, [
                'product_id' => $myProductVariant->myProduct->id,
                'product_variant_id' => $myProductVariant->id
            ]);
        });

        $myProduct->shopify_product_id = $shopifyProduct->id;
        $myProduct->save();

        $this
            ->putJson($myProduct->path(), ['title' => 'new product title'])
            ->assertResponseStatus(200)
            ->seeJsonContains(['title' => 'new product title'])
        ;
    }

    public function testDeleteVariantsSentToShopify(): void
    {
        Bus::fake();
        app()->bind(ShopifyService::class, FakeShopifyService::class);

        $this->signIn();
        create(Shop::class, ['shop' => 'test_shop.myshopify.com', 'user_id' => auth()->id()]);

        $myProducts = create(MyProduct::class, [
            'user_id' => auth()->id()
        ], 1)->each(function (MyProduct $myProduct) {
            $myProduct->options()->save(factory(MyProductOption::class)->make());
            $myProduct->combinations()->save(factory(MyProductVariant::class)->make());
            $myProduct->options()->save(factory(MyProductOption::class)->make());
            $myProduct->combinations()->each(function (MyProductVariant $myProductVariant) {
                create(ShopifyProductVariant::class, [
                    'product_id' => $myProductVariant->myProduct->id,
                    'product_variant_id' => $myProductVariant->id
                ]);
            });
        });

        /** @var MyProduct $myProduct */
        $myProduct = $myProducts->first();

        $shopifyProduct = create(ShopifyProduct::class, [
            'shopify_id' => 1,
            'user_id' => auth()->id(),
            'my_product_id' => $myProduct->id,
        ]);

        $myProduct->shopify_product_id = $shopifyProduct->id;
        $myProduct->save();

        $myProductVariant = MyProductVariant::where('my_product_id', $myProduct->id)->get()->first()->toArray();

        $response = $this->deleteJson('/api/my-products/'.$myProduct->id.'/variants/', [
            $myProductVariant['id']
        ]);

        $response
            ->assertResponseStatus(200)
            ->seeJsonContains(['result' => 'ok'])
            ->dontSeeInDatabase(MyProductVariant::getTableName(), $myProductVariant)
        ;
    }


    public function testEventDeleteVariant()
    {
        Bus::fake();
        $this->signIn();
        create(Shop::class, ['shop' => 'test_shop.myshopify.com', 'user_id' => auth()->id()]);

        create(MyProduct::class, [
            'user_id' => auth()->id()
        ], 1)->each(function (MyProduct $myProduct) {
            $myProduct->options()->save(factory(MyProductOption::class)->make());
            $myProduct->combinations()->save(factory(MyProductVariant::class)->make());
            $myProduct->options()->save(factory(MyProductOption::class)->make());
            $myProduct->combinations()->each(function (MyProductVariant $myProductVariant) {
                create(ShopifyProductVariant::class, [
                    'product_id' => $myProductVariant->myProduct->id,
                    'product_variant_id' => $myProductVariant->id
                ]);
            });
        });

        $myProduct = MyProduct::get()->first();

        $shopifyProduct = create(ShopifyProduct::class, [
            'shopify_id' => 1,
            'user_id' => auth()->id(),
            'my_product_id' => $myProduct->id,
        ]);

        $myProduct->shopify_product_id = $shopifyProduct->id;
        $myProduct->save();


        $myProductVariant = MyProductVariant::where('my_product_id', $myProduct->id)->get()->first()->toArray();

        $response = $this->deleteJson("api/my-products/{$myProduct->id}/variants/", [
            $myProductVariant['id']
        ]);

        $response
            ->assertResponseStatus(200)
            ->seeJsonContains(['result' => 'ok'])
        ;

        Bus::assertDispatched(SyncRemovedMyProductVariantsToShopify::class);
    }

}
