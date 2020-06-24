<?php

namespace Tests\Feature;

use App\Models\Product\Product;
use App\Models\Product\ProductVariant;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\UsesSqlite;

class ProductsTest extends TestCase
{
    use DatabaseMigrations, UsesSqlite;

    public function testGuestSeeProductsVariants(): void
    {
        $product = create(Product::class);
        $product->combinations()->save(make(ProductVariant::class));

        $this
            ->getJson(route('product.variants', ['product' => $product]))
            ->assertResponseOk()
            ->seeJson([
                'result' => 'ok',
                'product_id' => '1',
                'id' => 1
            ])
        ;
    }
}
