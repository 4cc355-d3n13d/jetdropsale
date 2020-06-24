<?php

namespace Tests\Feature;

use App\Models\Product\MyProduct;
use App\Models\Product\MyProductOption;
use App\Models\Product\MyProductTag;
use App\Models\Product\MyProductVariant;
use App\Models\Product\Product;
use App\Models\Product\ProductDetail;
use App\Models\Product\ProductOption;
use App\Models\Product\ProductVariant;
use App\Models\User;
use App\Services\MyProductService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits;

class CloneProductTest extends TestCase
{
    use DatabaseMigrations;
    use Traits\ArrangeThings;
    use Traits\UsesSqlite;

    public function testProduct(): void
    {
        $this->arrangeUserShop();
        $this->arrangeUserProducts();
        $user = User::lastOrFail();

        /** @var Product $product */
        $product = Product::firstOrFail();
        $myProductService = new MyProductService();
        $productDetail = new ProductDetail([
            'title' => 'zzz',
            'value' => 'xxx',
            'product_id' => $product->id,
        ]);
        $productDetail->save();

        $this->assertCount(16, ProductOption::all());
        $this->assertCount(8, ProductVariant::all());

        $addedMyProduct = $myProductService->clone($product, $user);
        self::assertStringEndsWith("{$productDetail->title}: {$productDetail->value}", $addedMyProduct->description);

        $this->assertCount(16, ProductOption::all());
        $this->assertCount(8, ProductVariant::all());

        $this->assertCount(2, MyProductOption::all());
        $this->assertCount(1, MyProductVariant::all());

        $this->checkInDB($product, $addedMyProduct);
    }

    public function testMyProduct(): void
    {
        $this->arrangeUserShop();
        $this->arrangeShopGoods();
        $user = User::lastOrFail();

        /** @var MyProduct $myProduct */
        $myProduct = MyProduct::firstOrFail();
        $myProductService = new MyProductService();
        $productDetail = new ProductDetail([
            'title' => 'zzz',
            'value' => 'xxx',
            'product_id' => $myProduct->product_id,
        ]);
        $productDetail->save();

        $this->assertCount(16, MyProductOption::all());
        $this->assertCount(10, MyProductVariant::all());
        $this->assertCount(1, $myProduct->collections()->get()->all());
        $this->assertCount(8, MyProductTag::all());

        $clonedMyProduct = $myProductService->clone($myProduct, $user);
        self::assertStringEndsWith("{$productDetail->title}: {$productDetail->value}", $clonedMyProduct->description);

        $this->assertCount(18, MyProductOption::all());
        $this->assertCount(11, MyProductVariant::all());
        $this->assertCount(1, $clonedMyProduct->collections()->get()->all());
        $this->assertCount(9, MyProductTag::all());

        $this->checkInDB($myProduct, $clonedMyProduct);
    }

    private function checkInDB($anyProduct, $clonedMyProduct): void
    {
        $this->seeInDatabase('my_products', [
            'product_id' => ($anyProduct instanceof MyProduct) ? $anyProduct->product_id : $anyProduct->id,
            'type' => $anyProduct->type,
            'vendor' => $anyProduct->vendor
        ]);
        $this->seeInDatabase('my_product_options', [
            'my_product_id' => $clonedMyProduct->id,
            'ali_sku' => $anyProduct->options()->first()->ali_sku,
        ]);
        $this->seeInDatabase('my_product_variants', [
            'my_product_id' => $clonedMyProduct->id,
            'product_variant_id' => ($anyProduct instanceof MyProduct) ? $anyProduct->combinations()->first()->product_variant_id : $anyProduct->combinations()->first()->id,
        ]);

        if ($anyProduct instanceof MyProduct) {
            $this->seeInDatabase('my_product_tags', [
                'my_product_id' => $clonedMyProduct->id,
                'user_id' => $anyProduct->user_id,
                'title' => $anyProduct->tags()->first()->title,
            ]);
            $this->seeInDatabase('my_product_has_collections', [
                'my_product_id' => $clonedMyProduct->id,
                'my_collection_id' => $anyProduct->collections()->first()->id,
            ]);
        }
    }
}
