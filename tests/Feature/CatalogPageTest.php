<?php

namespace Tests\Feature;

use App\Http\Controllers\Catalog\CatalogController;
use App\Models\Product\Product;
use App\Models\Product\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\UsesSqlite;

class CatalogPageTest extends TestCase
{
    use DatabaseMigrations, UsesSqlite;

    public function testCatalogPage(): void
    {
        $hasCategory = create(Category::class, ['parent_id' => 0]);

        $this->withoutExceptionHandling();

        $this
            ->visit('/catalog')
            //->assertViewHas('categories')
            ->assertResponseOk()
            ->see($hasCategory->title)
            ->see('/category/' . $hasCategory->id);
    }
}
