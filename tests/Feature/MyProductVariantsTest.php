<?php

namespace Tests\Feature;

use App\Models\Product\MyProduct;
use App\Models\Product\MyProductOption;
use App\Models\Product\MyProductVariant;
use App\Models\Shopify\Shop;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\UsesSqlite;

class MyProductVariantsTest extends TestCase
{
    use DatabaseMigrations, UsesSqlite;

    protected function setUp()
    {
        parent::setUp();

        $this->signIn();
        $count = (int) env('TEST_GENERATE_ITEM_COUNT');

        factory(MyProduct::class, $count)->create(['user_id' => auth()->id()])->each(function (MyProduct $product) {
            $product->options()->save(factory(MyProductOption::class)->make());
            $product->combinations()->save(factory(MyProductVariant::class)->make());
            $product->options()->save(factory(MyProductOption::class)->make());
            $product->combinations()->save(factory(MyProductVariant::class)->make());
        });
    }

    public function testGetMyProductVariantsInfo(): void
    {
        $myProduct = MyProduct::get()->first();

        $response = $this->getJson($myProduct->path('variants'));

        $response
            ->assertResponseOk()
            ->seeJson([
                'result' => 'ok',
            ])
            ->seeJsonStructure([
                'my_product_variants_info' => [
                    '*' => [
                        'id', 'price', 'amount',
                    ]
                ]
            ])
        ;
    }

    public function testFailingEditMyProductVariant(): void
    {
        $myProduct = MyProduct::get()->first();
        $myProductVariant = $myProduct->combinations()->first();

        $response = $this->putJson(
            "api/my-products/1111/variants/{$myProductVariant->id}",
            ['price' => '1234.56', 'amount' => '990']
        );

        $response
            ->assertResponseStatus(404)
            ->seeJsonContains(['result' => 'error'])
        ;

        $response = $this->putJson(
            $myProduct->path('variants/111'),
            ['price' => '1234.56', 'amount' => '990']
        );

        $response
            ->assertResponseStatus(404)
            ->seeJsonContains(['result' => 'error'])
        ;
    }

    public function testSuccessEditMyProductVariant(): void
    {
        $myProduct = MyProduct::get()->first();
        $myProductVariant = $myProduct->combinations()->first();

        $response = $this->putJson(
            $myProduct->path("variants/{$myProductVariant->id}"),
            ['price' => '1234.56', 'amount' => '990']
        );

        $response
            ->assertResponseOk()
            ->seeJson([
                'result' => 'ok',
            ])
            ->seeJsonStructure([
                'my_product_variant' => [
                    'id', 'my_product_id', 'sku', 'amount', 'price', 'combination', 'created_at', 'updated_at',
                ]
            ])
            ->seeJson([
                'id' => $myProduct['id'],
            ])
            ->dontSeeInDatabase(MyProductVariant::getTableName(), $myProductVariant->toArray())
        ;

        $myProductVariant->price = 1234.56;
        $myProductVariant->amount = 990;

        $newVariant = collect($myProductVariant)->forget('updated_at')->toArray();

        $this->seeInDatabase(MyProductVariant::getTableName(), $newVariant);
    }

    public function testFailingDeleteMyProductVariant(): void
    {
        $myProduct = MyProduct::get()->first();
        $myProductVariant = MyProductVariant::where('my_product_id', $myProduct->id)->get()->first()->toArray();

        $response = $this->deleteJson("api/my-products/1111/variants/{$myProductVariant['id']}");

        $response
            ->assertResponseStatus(404)
            ->seeJsonContains(['result' => 'error'])
        ;

        $response = $this->deleteJson($myProduct->path('variants/11'));

        $response
            ->assertResponseStatus(404)
            ->seeJsonContains(['result' => 'error'])
        ;
    }

    public function testSuccessDeleteMyProductVariant(): void
    {
        $myProduct = MyProduct::get()->first();
        $myProductVariant = MyProductVariant::where('my_product_id', $myProduct->id)->get()->first()->toArray();

        $response = $this->deleteJson($myProduct->path("variants/{$myProductVariant['id']}"));

        $response
            ->assertResponseStatus(200)
            ->seeJsonContains(['result' => 'ok'])
            ->dontSeeInDatabase(MyProductVariant::getTableName(), $myProductVariant)
        ;
    }

    public function testSuccessEditMyProductVariants(): void
    {
        $myProduct = MyProduct::get()->first();
        $myProductVariant = $myProduct->combinations()->first();

        $response = $this->putJson(
            $myProduct->path('variants'),
            [
                $myProductVariant->id => ['price' => '111.11', 'amount' => '111']
            ]
        );

        $response
            ->assertResponseOk()
            ->seeJson([
                'result' => 'ok',
            ])
            ->seeJsonStructure([
                'my_product_variants' => [
                    0 => [
                        'amount',
                        'combination',
                        'created_at',
                        'id',
                        'my_product_id',
                        'price',
                        'sku',
                        'updated_at'
                    ]
                ]
            ])
            ->seeJson([
                'id' => $myProduct['id'],
                'amount' => '111',
                'price' => '111.11',
            ])
            ->dontSeeInDatabase(MyProductVariant::getTableName(), $myProductVariant->toArray())
        ;

        $myProductVariant->price = 111.11;
        $myProductVariant->amount = 111;

        $newVariant = collect($myProductVariant)->forget('updated_at')->toArray();

        $this->seeInDatabase(MyProductVariant::getTableName(), $newVariant);
    }

    public function testFailingEditMyProductVariants(): void
    {
        $myProduct = MyProduct::get()->first();
        $myProductVariant = $myProduct->combinations()->first();

        $response = $this->putJson(
            'api/my-products/111/variants',
            [
                $myProductVariant->id => ['price' => '111.11', 'amount' => '111']
            ]
        );

        $response
            ->assertResponseStatus(404)
            ->seeJsonContains(['result' => 'error'])
        ;

        $response = $this->putJson(
            $myProduct->path('variants'),
            [
                111 => ['price' => '111.11', 'amount' => '111']
            ]
        );

        $response
            ->assertResponseStatus(404)
            ->seeJsonContains(['result' => 'error'])
        ;
    }

    public function testSuccessDeleteMyProductVariants(): void
    {
        $myProduct = MyProduct::get()->first();
        $myProductVariantsIds = MyProductVariant::where('my_product_id', $myProduct->id)->get()->pluck('id')->toJson();
        $myProductVariants = MyProductVariant::where('my_product_id', $myProduct->id)->get()->toArray();

        $response = $this->deleteJson($myProduct->path('variants'), json_decode($myProductVariantsIds));

        $response
            ->assertResponseStatus(200)
            ->seeJsonContains(['result' => 'ok'])
        ;

        foreach ($myProductVariants as $myProductVariant) {
            $this->dontSeeInDatabase(MyProductVariant::getTableName(), $myProductVariant);
        }
    }

    public function testFailingDeleteMyProductVariants(): void
    {
        $myProduct = MyProduct::get()->first();
        $myProductVariantsIds = MyProductVariant::where('my_product_id', $myProduct->id)->get()->pluck('id')->toJson();
        $myProductVariants = MyProductVariant::where('my_product_id', $myProduct->id)->get()->toArray();

        $response = $this->deleteJson('api/my-products/1234/variants', json_decode($myProductVariantsIds));

        $response
            ->assertResponseStatus(404)
            ->seeJsonContains(['result' => 'error'])
        ;

        foreach ($myProductVariants as $myProductVariant) {
            $this->seeInDatabase(MyProductVariant::getTableName(), $myProductVariant);
        }
    }

    public function testSuccessDeleteMyProducts(): void
    {
        $myProduct1 = create(MyProduct::class, ['user_id' => auth()->id()]);
        $myProduct2 = create(MyProduct::class, ['user_id' => auth()->id()]);
        // Needed for the check inside removal method
        $shop = create(Shop::class, ['user_id'=>auth()->id()]);
        $myProduct1 = create(MyProduct::class, ['user_id'=>auth()->id()]);
        $myProduct2 = create(MyProduct::class, ['user_id'=>auth()->id()]);

        $idsToRemove = [$myProduct1->id, $myProduct2->id];
        $response = $this->deleteJson('api/my-products', $idsToRemove);

        $response
            ->assertResponseStatus(200)
            ->seeJsonContains(['result' => 'ok'])
            ->seeJsonContains(['deleted_my_product_ids' => $idsToRemove])
        ;

        $myProducts = MyProduct::whereIn('id', $idsToRemove)->withTrashed()->get();
        foreach ($myProducts as $myProduct) {
            $this->seeInDatabase('my_products', ['id' => $myProduct->id]);
            $this->notSeeInDatabase('my_products', ['id' => $myProduct->id, 'deleted_at' => null]);
        }
    }

    public function testFailingDeleteMyProducts(): void
    {
        // Needed for the check inside removal method
        $shop = create(Shop::class, ['user_id'=>auth()->id()]);
        $idsToRemove = [111, 222];
        $response = $this->deleteJson('api/my-products', $idsToRemove);

        $response
            ->assertResponseStatus(404)
            ->seeJsonContains(['result' => 'error'])
        ;
    }
}
