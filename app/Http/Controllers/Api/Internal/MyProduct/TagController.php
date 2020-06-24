<?php

namespace App\Http\Controllers\Api\Internal\MyProduct;

use App\Http\Controllers\Api\Internal\ApiController;
use App\Http\Resources\TagCollection;
use App\Http\Resources\TagItem;
use App\Models\Product\MyProduct;
use App\Models\Product\MyProductTag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Tag",
 *     @OA\Property(property="id", type="integer", example="10042"),
 *     @OA\Property(property="title", type="string", example="Winter 2018"),
 * )
 */
class TagController extends ApiController
{
    /**
     * @OA\Get(path="/api/my-products/tags",
     *     tags={"MyProducts", "Collections"},
     *     summary="Get users Tag list",
     *     description="Get users collection list",
     *     @OA\Response(response="200", description="Tag list", @OA\JsonContent(
     *         allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *         @OA\Property(property="collections", @OA\Items(
     *             @OA\Property(property="title", type="string", example="winter"),
     *         ))
     *     ))
     * )
     */
    public function find(): JsonResource
    {
        return new TagCollection(auth()->user()->tags);
    }

    /**
     * @OA\Post(path="/api/my-products/{product_id}/tags",
     *     tags={"MyProducts", "Tags"},
     *     summary="Add a tag",
     *     description="Create new `MyProductTag` model",
     *     @OA\RequestBody(required=true, description="New tag title", @OA\JsonContent(
     *         @OA\Property(property="title", type="string", example="Winter 2018"),
     *         @OA\Property(property="my_product_id", type="int", example="10410")
     *     )),
     *     @OA\Response(response="200", description="Tag was successfully added", @OA\JsonContent(
     *         allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *         @OA\Property(property="tag", type="array", @OA\Items(
     *              allOf={@OA\Schema(ref="#/components/schemas/Tag")}
     *         ))
     *     ))
     * )
     */
    public function create(MyProduct $myProduct): JsonResource
    {
        $tag = MyProductTag::create([
            'title' => request()->post('title'),
            'user_id' => auth()->id(),
            'my_product_id' => $myProduct->id,
        ]);

        return new TagItem($tag, 'tag');
    }


    /**
     * @OA\Delete(path="/api/my-products/tags/{tag_id}",
     *     tags={"MyProducts", "Tags"},
     *     summary="Remove a tag",
     *     description="Update `MyProductTag` title",
     *     @OA\Response(response="200", description="Tag was successfully updated", @OA\JsonContent(
     *         allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *         @OA\Property(property="tag", type="array", @OA\Items(
     *              allOf={@OA\Schema(ref="#/components/schemas/Tag")}
     *         ))
     *     )),
     *     @OA\Response(response="404", description="No tag with given id found",
     *        @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        MyProductTag::where(['id' => $id, 'user_id' => auth()->id()])->delete();

        return $this->success('The tag was successfully deleted');
    }
}
