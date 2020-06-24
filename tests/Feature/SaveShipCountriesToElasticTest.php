<?php

namespace Tests\Feature;

use App\Models\Product\Product;
use App\Models\Product\ProductOption;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

// todo: move to elastic namespace
class SaveShipCountriesToElasticTest extends TestCase
{
    use DatabaseMigrations;
    use WithoutMiddleware;

    public function testAliProductSearchableSaving(): void
    {
        $product = factory(Product::class)->create();

        $product->options()
            ->save(factory(ProductOption::class)
            ->make([
                'product_id' => $product->id,
                'name' => 'Ships from',
                'ali_option_id' => ProductOption::SHIPPING_FROM_OPTION,
                'value' => 'china'
            ])
        );

        $this->assertArrayHasKey('ship_countries', $product->toSearchableArray());
        $this->assertContains('china', $product->toSearchableArray()['ship_countries']);
    }
}
