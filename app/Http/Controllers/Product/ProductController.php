<?php

namespace App\Http\Controllers\Product;

use App\Enums\ProductStatusType;
use App\Http\Controllers\WebController;
use App\Models\Order;
use App\Models\Product\Product;
use App\Models\ShipGoods;
use App\Services\ProductService;
use Artesaos\SEOTools\Traits\SEOTools;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

/**
 * Class ProductController
 */
class ProductController extends WebController
{
    use SEOTools;

    /**
     * @var ProductService
     */
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display the specified resource.
     * @param Product $product
     * @return Factory|View
     */
    public function show(Product $product)
    {
        abort_if($product->status == ProductStatusType::HIDDEN, 404);
        $this->seo()
            ->setTitle("Dropwow {$product->title}")
            ->setCanonical(route('product.show', $product))
            //->setDescription("Dropship high quality [SUBCATEGORY 1] and [SUBCATEGORY 2] productsfor low price with Dropwow.")
            ->metatags()->setKeywords([
                //  "sell [SUBCATEGORY 1] products",
                //  "sell [SUBCATEGORY 2] products",
                //  "trending [SUBCATEGORY 1]",
                //"trending [SUBCATEGORY 2]",
                "best selling dropshipping items"
            ]);

        $this->seo()->opengraph()
            ->setTitle($product->title)
            ->setUrl(route('product.show', $product))
            ->addImage($product->image)
            ->setType('product');

        $related = Product
            ::search(preg_replace('/[^A-Za-z0-9 ]/', '', $product->title))
            ->where('categoriesPath.keyword', $product->categoriesPath)
            ->where('id', '!=', $product->id)
            ->take(8)->get();

        return view('product.product', [
            'product' => $product,
            'shippingPrice' => resolve(ShipGoods::class)->price,
            'related' => $related
        ])->render();
    }
}
