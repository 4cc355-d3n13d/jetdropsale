<?php

namespace Tests\Feature;

use App\Models\Product\Product;
use App\Models\Product\Category;
use Laravel\BrowserKitTesting\Constraints\HasSource;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Traits\UsesSqlite;

class CatalogSearchPageTest extends TestCase
{
    use DatabaseMigrations, UsesSqlite;

    public const SEARCHQUERY = 'any query';

    public function testCatalogPage(): void
    {
        $this
            ->visit(route('catalog.search', ['query' => $this::SEARCHQUERY]))
            ->assertResponseOk()
            ->assertInPage(new HasSource('Search:'))
            ->assertInPage(new HasSource($this::SEARCHQUERY))
        ;
    }

    public function testCategorySearchShipCountryPage(): void
    {
        /** @var Category $productCategory */
        $productCategory = create(Category::class);

        /** @var Product $product */
        $product = create(Product::class);

        $productCategory->products()->save(
            $product
        );

        $this
            ->visit(route('category.products', ['category'=>$productCategory, 'ships_country'=>'usa']))
            ->assertResponseOk()
        ;

        $this
            ->visit(route('category.products', ['category'=>$productCategory, 'ships_country'=>'china']))
            ->assertResponseOk()
        ;

        $this
            ->visit(route('category.products', ['category'=>$productCategory, 'pmin'=>'1']))
            ->assertResponseOk()
        ;

        $this
            ->visit(route('category.products', ['category'=>$productCategory, 'pmax'=>'100000']))
            ->assertResponseOk()
        ;

        $this
            ->visit(route('category.products', ['category'=>$productCategory, 'pmin'=>'1', 'pmax'=>100500]))
            ->assertResponseOk()
        ;
    }
}
