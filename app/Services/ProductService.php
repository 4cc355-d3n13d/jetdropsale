<?php

namespace App\Services;

use App\Models\Product\Product;
use ScoutElastic\Builders\FilterBuilder;
use ScoutElastic\Builders\SearchBuilder;

/**
 * Class ProductService
 */
class ProductService
{
    /**  @var int $id */
    protected $id;

    public function getProduct(int $id): Product
    {
        $product = Product::search('*')->where('id', $id)->with(['options', 'combinations'])->first();
        $product = ! empty($product) ? $product : Product::with(['options', 'combinations'])->findOrFail($id);

        return $product;
    }

    /**
     * @return FilterBuilder|SearchBuilder|string
     */
    public function getProducts()
    {
        $products = Product::search('*');
        /** @noinspection IsEmptyFunctionUsageInspection */
        $products = ! empty($products) ? $products : Product::class;

        return $products;
    }

    /**
     * @return mixed
     */
    public function countAllProducts()
    {
        return Product::count();
    }
}
