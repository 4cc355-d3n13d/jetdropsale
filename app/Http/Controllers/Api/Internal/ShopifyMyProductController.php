<?php

namespace App\Http\Controllers\Api\Internal;

use App\Enums\MyProductStatusType;
use App\Jobs\Shopify\SendProductToShopify;
use App\Models\Product\MyProduct;
use App\Models\Shopify\Shop;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class ShopifyMyProductController extends ApiController
{
    /**
     * @OA\Post(path="/api/my-products/shopify/send",
     *     tags={"MyProducts"},
     *     summary="Send MyProduct to Shopify",
     *     description="Send MyProduct to Shopify",
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="ids", type="array", example={1,2,3}, @OA\Items(type="integer", format="int32", minimum="1"))
     *     )),
     *     @OA\Response(response="200", description="My product, sent to Shopify", @OA\JsonContent(
     *         @OA\Property(property="result", example="ok"),
     *         @OA\Property(property="my_products", type="array",
     *             @OA\Items(@OA\Property(property="result", type="string", example="ok")),
     *             @OA\Items(@OA\Property(property="message", type="string", example="Products were sent to Shopify"))
     *         )
     *     ))
     * )
     */
    public function sendMyProducts(Request $request): JsonResponse
    {
        if (! $shop = Shop::where(['user_id' => auth()->id()])->first()) {
            abort(Response::HTTP_NOT_FOUND, 'Cannot find shop for user ' . auth()->id());
        }

        $myProducts = [];
        $productIds = $request->input('ids');
        foreach ($productIds as $productId) {
            try {
                $myProduct = MyProduct::findOrFail($productId);
            } catch (ModelNotFoundException $e) {
                $myProducts[$productId] = [
                    'result' => 'error',
                    'message' => 'Product not found',
                ];
                continue;
            }

            if (! $myProduct->shopify_product_id) {
                $myProducts[$productId] = [
                    'result' => 'ok',
                ];

                $myProduct->status = MyProductStatusType::SHOPIFY_SEND_PENDING;
                $myProduct->connected_at = now();
                $myProduct->save();
                SendProductToShopify::dispatch($shop, $myProduct);
            } else {
                $myProducts[$productId] = [
                    'result' => 'error',
                    'message' => 'Product already added to shopify',
                ];
            };
        }

        return $this->success([
            'my_products' => $myProducts,
            'message' => 'Products were sent to Shopify'
        ]);
    }
}
