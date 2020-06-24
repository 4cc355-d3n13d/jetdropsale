<?php

namespace App\Http\Controllers\Api\Internal;

use App\Models\Product\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

/**
 * Class ProductController
 * @deprecated Accessed by requesting web-page directly w/o any API
 */
class ProductController extends ApiController
{
    /**
     * @OA\Post(path="/api/products",
     *     tags={"Products"},
     *     summary="...",
     *     description="Создание нового продукта",
     *     @OA\Response(response="default", description="successful operation")
     * )
     */

    /**
     * @OA\Post(path="/api/products/{product_id}/cover_image",
     *     tags={"Products", "Image"},
     *     summary="...",
     *     description="Создание картинки обложки продукта",
     *     @OA\Parameter(ref="#/components/parameters/product_id"),
     *     @OA\Response(response="default", description="successful operation")
     * )
     */

    /**
     * @OA\Post(path="/api/products/{product_id}/images",
     *     tags={"Products", "Image"},
     *     summary="...",
     *     description="Создание картинок для слайдера продукта",
     *     @OA\Parameter(ref="#/components/parameters/product_id"),
     *     @OA\Response(response="default", description="successful operation")
     * )
     */

    /** @var ProductService */
    protected $productService;


    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @OA\Get(path="/api/products/{product_id}/images",
     *     tags={"Products"},
     *     summary="Get all images of Product",
     *     description="Get all images of Product by **`product_id`**",
     *     @OA\Parameter(ref="#/components/parameters/product_id"),
     *      @OA\Response(response="200", description="Product's images list successfully extracted",
     *         @OA\JsonContent(
     *             allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *             @OA\Property(property="images", type="array", @OA\Items(ref="#/components/schemas/ProductImages"))
     *         )
     *     )
     * )
     */
    public function getProductImages(int $id): JsonResponse
    {
        return response()->json(
            [
                'result' => 'ok',
                'images' => json_decode($this->productService->getProduct($id)->images),
            ],
            200
        );
    }

    /**
     * @OA\Get(path="/api/products/{product_id}/options/images",
     *     tags={"Products"},
     *     summary="Get all images of all ProductOptions",
     *     description="Get all images of all ProductOptions by **`product_id`**",
     *     @OA\Parameter(ref="#/components/parameters/product_id"),
     *     @OA\Response(response="200", description="ProductOptions' images list successfully extracted",
     *         @OA\JsonContent(
     *             allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *             @OA\Property(property="images", type="array", @OA\Items(ref="#/components/schemas/ProductImages"))
     *         )
     *     )
     * )
     */
    public function getProductOptionsImages(int $id): JsonResponse
    {
        $product = $this->productService->getProduct($id);
        $productOptionsImages = $product->options ? json_decode($product->options->pluck('image')) : [];

        return response()->json(array_diff($productOptionsImages, ['']), 200);
    }

    /**
     * @OA\Get(path="/api/products/{product_id}/variants",
     *     tags={"Products"},
     *     summary="Get amount and price of ProductVariants",
     *     description="Get info about amount and price of ProductVariants by **`product_id`**",
     *     @OA\Parameter(ref="#/components/parameters/product_id"),
     *     @OA\Response(response="200", description="ProductVariants' info successfully extracted",
     *         @OA\JsonContent(
     *            allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *            @OA\Property(property="product_variants_info", @OA\Items(ref="#/components/schemas/VariantsInfo"))
     *          )
     *     )
     * )
     */
    public function variants(Product $product)
    {
        $variantsInfo = $product->combinations->toArray();

        $mapVariantsInfo = [];
        foreach ($variantsInfo as $variantInfo) {
            $variantId = implode(
                '-',
                array_sort(array_values(collect(json_decode($variantInfo['combination']))->toArray()))
            );
            unset($variantInfo['combination']);
            $mapVariantsInfo[$variantId] = $variantInfo;
        }

        return response()->json(
            [
                'result' => 'ok',
                'product_variants_info' => $mapVariantsInfo
            ],
            200
        );
    }
}
