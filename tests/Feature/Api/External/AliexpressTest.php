<?php

namespace Tests\Feature\Api\External;

use App\Enums\ProductStatusType;
use App\Jobs\ImportAliProduct;
use App\Jobs\ProcessInvoicePayment;
use App\Models\Product\Product;
use App\SuperClass\Facades\Hashids;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class AliexpressTest extends TestCase
{
    use DatabaseMigrations;
    use WithoutMiddleware;

    public function testAliProductDispatchingQueuedJob(): void
    {
        Bus::fake();

        // Perform product sending...
        $productData = $this->mockJson('AliProduct.txt');
        $this
            ->postJson('/test/ali/save', $productData, ['host'=>'api.dropwow.loc'])
            ->seeStatusCode(200)
            ->seeJson(['result' => 'ok'])
        ;

        // Assert the job was dispatched...
        Bus::assertDispatched(ImportAliProduct::class, function ($job) use ($productData) {
            return $job->productData['product_code'] === $productData['product_code'];
        });

        // Assert a job was not dispatched...
        Bus::assertNotDispatched(ProcessInvoicePayment::class);
    }

    public function testAliProductSaving(): void
    {
        $productData = $this->mockJson('AliProduct.txt');
        $apiResponse = $this->postJson('/test/ali/save', $productData);

        $apiResponse->seeStatusCode(200)->seeJson(['result' => 'ok']);

        $this->seeInDatabase('products', [
            'id' => 1,
            'title' => $productData['product'],
            'price' => $productData['price'],
            'amount' => $productData['amount'],
            'description' => $productData['description'],
            'categoriesPath' => '1/2/3'
        ]);

        $this->seeInDatabase('product_variants', [
            'id' => 6,
            'product_id' => 1,
            'price' => 7.21,
            'amount' => 918,
            'sku' => Hashids::encode([1, 200004194]),
            ['combination', 'like', '{"200000463": "200004194"}']
        ]);

        $this->seeInDatabase('product_options', [
            'id' => 6,
            'product_id' => 1,
            'name' => 'Kid US Size',
            'value' => 8,
            'ali_sku' => 200004194,
            'ali_option_id' => 200000463
        ]);
    }

    public function testGetIdsToUpdate()
    {
        create(Product::class, ['ali_id' => 1, 'is_available' => true]);
        create(Product::class, ['ali_id' => 2, 'is_available' => true]);

        create(Product::class, ['ali_id' => 3, 'is_available' => false]);

        $this->getJson('/test/ali/list')->seeJson(['products' => [1,2]]);
    }

    public function testSetAvailableToUnavailable()
    {
        $product = create(Product::class, ['ali_id' => 1, 'is_available' => true]);
        $this->assertEquals(ProductStatusType::AVAILABLE, $product->is_available);
        $this->postJson('/test/ali/delete', ['product_code' => $product->ali_id]);
        $product->refresh();
        $this->assertEquals(ProductStatusType::UNAVAILABLE, $product->is_available);
    }
}
