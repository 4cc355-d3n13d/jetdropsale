<?php

namespace Tests\Feature;

use App\Models\Product\MyProduct;
use App\Models\Product\Product;
use App\Models\Product\ProductOption;
use App\Models\Product\ProductVariant;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\UsesSqlite;

class AddToImportListTest extends TestCase
{
    use DatabaseMigrations, UsesSqlite;

    public function testFailingAddNonExistentProduct(): void
    {
        $this->signIn()
            ->postJson('/api/products/1/my/add')
            ->seeStatusCode(404)
            ->seeJson(['result' => 'error'])
        ;
    }

    public function testAddExistentProduct(): void
    {
        $this->seed();
        $getId = random_int(1, (int) env('TEST_GENERATE_ITEM_COUNT'));

        $this->signIn()
            ->postJson("/api/products/{$getId}/my/add")
            ->assertResponseOk()
            ->seeJson(['result' => 'ok'])
            ->seeInDatabase('my_products', ['product_id' => $getId])
        ;

        $myProduct = MyProduct::where('product_id', $getId)->first();

        $this->seeInDatabase('my_product_options', ['my_product_id' => $myProduct->id]);
        $this->seeInDatabase('my_product_variants', ['my_product_id' => $myProduct->id]);
    }

    public function testAddExistentProductWithPriceMultiply(): void
    {
        $product = create(Product::class);
        $product->options()->save(factory(ProductOption::class)->make());
        $product->combinations()->save(factory(ProductVariant::class)->make());

        $this->signIn();

        $settings = [
            'gpr_rate' => '1.1',
            'gpr_type' => 'm'
        ];

        $this->putJson("/api/user/settings", $settings)
            ->assertResponseStatus(200)
            ->seeJsonSubset(['settings' => $settings])
        ;

        $this
            ->postJson("/api/products/{$product->id}/my/add")
            ->assertResponseOk()
            ->seeJson(['result' => 'ok'])
            ->seeInDatabase('my_products', ['product_id' => $product->id])
        ;

        $product = Product::where('id', $product->id )->first();

        $myProduct = MyProduct::where('product_id', $product->id)->first();

        $this->assertNotEquals($myProduct->price, $product->price);
        $this->assertEquals($myProduct->price, $product->price * 1.1);

        $this->seeInDatabase('my_product_options', ['my_product_id' => $myProduct->id]);
        $this->seeInDatabase('my_product_variants', ['my_product_id' => $myProduct->id]);
    }

    public function testAddExistentProductWithPriceAdd(): void
    {
        $product = create(Product::class);
        $product->options()->save(factory(ProductOption::class)->make());
        $product->combinations()->save(factory(ProductVariant::class)->make());

        $this->signIn();

        $settings = [
            'gpr_rate' => '100',
            'gpr_type' => 'f'
        ];

        $this->putJson("/api/user/settings", $settings)
            ->assertResponseStatus(200)
            ->seeJsonSubset(['settings' => $settings])
        ;

        $this
            ->postJson("/api/products/{$product->id}/my/add")
            ->assertResponseOk()
            ->seeJson(['result' => 'ok'])
            ->seeInDatabase('my_products', ['product_id' => $product->id])
        ;

        $product = Product::where('id', $product->id )->first();

        $myProduct = MyProduct::where('product_id', $product->id)->first();

        $this->assertNotEquals($myProduct->price, $product->price);
        $this->assertEquals($myProduct->price, $product->price + 100);

        $this->seeInDatabase('my_product_options', ['my_product_id' => $myProduct->id]);
        $this->seeInDatabase('my_product_variants', ['my_product_id' => $myProduct->id]);
    }
}
