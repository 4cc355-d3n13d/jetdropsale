<?php

namespace App\Http\Controllers\Api\Internal\MyProduct;

use App\Enums\ShopifyCollectionType;
use App\Http\Controllers\Api\Internal\ApiController;
use App\Http\Resources\CollectionCollection;
use App\Http\Resources\CollectionItem;
use App\Models\Product\MyProductCollection;
use App\Services\MyProductService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Collection",
 *     @OA\Property(property="id", type="integer", example="1120"),
 *     @OA\Property(property="title", type="string", example="Winter Collection 2018"),
 * )
 */
class CollectionController extends ApiController
{
    /** @var ProductService $productService
     */
    protected $productService;

    /** @var MyProductService $myProductService */
    protected $myProductService;

    public function __construct(ProductService $productService, MyProductService $myProductService)
    {
        $this->productService = $productService;
        $this->myProductService = $myProductService;
    }


    /**
     * @OA\Get(path="/api/my-products/collections",
     *     tags={"MyProducts", "Collections"},
     *     summary="Get users collection list",
     *     description="Get users collection list",
     *     @OA\Response(response="200", description="Users collection list", @OA\JsonContent(
     *         allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *         @OA\Property(property="collections", @OA\Items(
     *             @OA\Property(property="id", type="integer", example="1"),
     *             @OA\Property(property="title", type="string", example="winter"),
     *         ))
     *     ))
     * )
     */
    public function find(): JsonResource
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return new CollectionCollection(auth()->user()->collections()->select('id', 'title')->distinct()->get());
    }

    /**
     * @OA\Post(path="/api/my-products/collections",
     *     tags={"MyProducts", "Collections"},
     *     summary="Add a new collection",
     *     description="Create new `MyCollection` model",
     *     @OA\RequestBody(required=true, description="New collection title", @OA\JsonContent(
     *         @OA\Property(property="title", type="string", example="Winter 2018")
     *     )),
     *     @OA\Response(response="200", description="The collection was successfully added", @OA\JsonContent(
     *         allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *         @OA\Property(property="collection", type="array", @OA\Items(
     *              allOf={@OA\Schema(ref="#/components/schemas/Collection")}
     *         ))
     *     ))
     * )
     */
    public function create(): JsonResource
    {
        $collection = MyProductCollection::create([
            'user_id' => auth()->id(),
            'type' => ShopifyCollectionType::CUSTOM,
            'title' => request()->post('title'),
        ]);

        return new CollectionItem($collection, 'collection');
    }

    /**
     * @OA\Delete(path="/api/my-products/collections/{collection_id}",
     *     tags={"MyProducts", "Collections"},
     *     summary="Remove a collection",
     *     description="Update `MyCollection` title",
     *     @OA\Response(response="200", description="The collection was successfully deleted", @OA\JsonContent(
     *         allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *         @OA\Property(property="collection", type="array", @OA\Items(
     *              allOf={@OA\Schema(ref="#/components/schemas/Collection")}
     *         ))
     *     )),
     *     @OA\Response(response="404", description="No collection with given id found",
     *        @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        MyProductCollection::where(['id' => $id, 'user_id' => auth()->id()])->delete();

        return $this->success('The collection was successfully deleted');
    }
}
