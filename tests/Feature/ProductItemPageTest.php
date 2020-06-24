<?php

namespace Tests\Feature;

use App\Models\Product\Product;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\UsesSqlite;

class ProductItemPageTest extends TestCase
{
    use DatabaseMigrations, UsesSqlite;

    public function testSeeProductOnPage(): void
    {
        $product = create(Product::class);
        cache()->forget(Product::getViewCacheKey($product->id));
        $this
            ->visit('/product/' . $product->id)
            ->see($product->title)
           // ->assertViewHas('product')
            ->assertResponseOk()
        ;

        $this->assertInstanceOf(Product::class, $product);
    }
}
