<?php

namespace App\Http\Controllers\Api\Internal;

use App\ApiSerializer;
use App\Enums\MyProductStatusType;
use App\Enums\ShopifyCollectionType;
use App\Filters\MyProductFilter;
use App\Http\Transformers\MyProductTransformer;
use App\Jobs\Shopify\RemoveProductInShopify;
use App\Models\Product\MyProduct;
use App\Models\Product\MyProductCollection;
use App\Models\Product\MyProductOption;
use App\Models\Product\MyProductTag;
use App\Models\Product\MyProductVariant;
use App\Models\Product\Product;
use App\Models\Product\ProductOption;
use App\Models\Shopify\Shop;
use App\Models\User;
use App\Services\MyProductService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class MyProductController extends ApiController
{
    /** @var ProductService $productService */
    protected $productService;

    /** @var MyProductService $myProductService */
    protected $myProductService;

    public function __construct(ProductService $productService, MyProductService $myProductService)
    {
        $this->productService = $productService;
        $this->myProductService = $myProductService;
    }

    /**
     * @OA\Post(
     *   tags={"Products", "MyProducts"},
     *   path="/api/products/{product_id}/my/add",
     *   summary="Add Product to user's MyProduct",
     *   description="Adding product to users's 'MyProducts' ('cloning')",
     *   @OA\Parameter(ref="#/components/parameters/product_id"),
     *   @OA\Response(response="200", description="The Product was added to user's 'MyProducts'",
     *      @OA\JsonContent(ref="#/components/schemas/MyProduct")
     *   ),
     *   @OA\Response(response="404", description="The MyProduct was not found",
     *      @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *   )
     * )
     * @param int $id
     */
    public function addProduct($id): JsonResponse
    {
        if (! is_numeric($id)) {
            report(new \RuntimeException('Product not found: ' . var_export($id, true)));
            abort(Response::HTTP_BAD_REQUEST, 'Product not found');
        }

        /** @var User $user */
        $user = Auth::user();

        /** @var Product $product */
        $product = $this->productService->getProduct($id);

        $productOptionsValues = $product->options->pluck('value', 'id')->toArray();
        $countOptionsValues = $productOptionsValues ? array_count_values($productOptionsValues) : [];

        $product->options->map(function (ProductOption $option) use ($countOptionsValues) {
            $option->value = ($countOptionsValues && $countOptionsValues[$option->value] > 1)
                ? $option->value . ' - ' . $option->id
                : $option->value
            ;

            return $option;
        });

        $myProduct = $this->myProductService->clone($product, $user);

        return $this->success(['data' => $myProduct->toArray()]);
    }

    /**
     * @OA\Post(
     *   tags={"MyProducts"},
     *   path="/api/my-products/{my_product_id}/clone",
     *   summary="Clone MyProduct to MyProduct",
     *   description="Cloning MyProduct to 'MyProducts'",
     *   @OA\Parameter(ref="#/components/parameters/my_product_id"),
     *   @OA\Response(response="200", description="The MyProduct was added to user's 'MyProducts'",
     *      @OA\JsonContent(ref="#/components/schemas/MyProduct")
     *   ),
     *   @OA\Response(response="404", description="The MyProduct was not found",
     *      @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *   )
     * )
     */
    public function clone(MyProduct $myProduct): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $myProductOptionsValues = $myProduct->options->pluck('value', 'id')->toArray();
        $countOptionsValues = $myProductOptionsValues ? array_count_values($myProductOptionsValues) : [];

        $myProduct->options->map(function (MyProductOption $option) use ($countOptionsValues) {
            $option->value = ($countOptionsValues && $countOptionsValues[$option->value] > 1) ? $option->value . ' - ' . $option->id : $option->value;
            return $option;
        });

        $clonedMyProduct = $this->myProductService->clone($myProduct, $user);

        return $this->success(['data' => $clonedMyProduct->toArray()]);
    }

    /**
     * @OA\Post(
     *   tags={"MyProducts"},
     *   path="/api/my-products/{my_product_id}/split/{option_id}",
     *   summary="Split MyProduct by MyProduct' option_id",
     *   description="Splitting MyProduct by MyProduct' option_id",
     *   @OA\Parameter(ref="#/components/parameters/my_product_id"),
     *   @OA\Parameter(ref="#/components/parameters/option_id"),
     *   @OA\Response(response="200", description="The MyProduct was added to user's 'MyProducts' with only selected option.",
     *      @OA\JsonContent(ref="#/components/schemas/MyProduct")
     *   ),
     *   @OA\Response(response="404", description="The MyProduct was not found",
     *      @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *   )
     * )
     */
    public function split(MyProduct $myProduct, int $option_id): JsonResponse
    {
        /** @noinspection PhpParamsInspection */
        return $this->success(['data' => $this->myProductService->split($myProduct, auth()->user(), $option_id)->toArray()]);
    }

    /**
     * @OA\Get(path="/api/my-products/{my_product_id}",
     *   tags={"MyProducts"},
     *   summary="Get MyProduct",
     *   description="Get MyProduct by **`my_product_id`**",
     *   @OA\Parameter(ref="#/components/parameters/my_product_id"),
     *   @OA\Response(response="200", description="The MyProduct was successfully extracted",
     *      @OA\JsonContent(
     *         allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *         @OA\Property(property="product",type="array",
     *            @OA\Items(
     *               allOf={@OA\Schema(ref="#/components/schemas/MyProduct")},
     *                  @OA\Property(property="options", type="array", @OA\Items(ref="#/components/schemas/MyProductOptions")),
     *                  @OA\Property(property="combinations", type="array", @OA\Items(ref="#/components/schemas/MyProductVariants"))
     *               )
     *            ),
     *         @OA\Property(property="product_variants_info", @OA\Items(ref="#/components/schemas/MyVariantsInfo"))
     *      )
     *   ),
     *   @OA\Response(response="404", description="The MyProduct was not found",
     *      @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *   )
     * )
     */
    public function view(MyProduct $myProduct): JsonResponse
    {
        /** @var MyProduct $myProduct */
        $myProduct = MyProduct::with(['options', 'combinations', 'shopifyProduct', 'tags', 'collections'])->findOrFail($myProduct->id);
        $myProduct->images = json_decode($myProduct->images);

        $variantsInfo = MyProductVariant
            ::select(['combination', 'price', 'amount'])
            ->where('my_product_id', $myProduct->id)
            ->get()
            ->toArray()
        ;
        $mapVariantsInfo = [];
        foreach ($variantsInfo as $variantInfo) {
            $variantId = implode(
                '-',
                array_sort(array_values(collect(json_decode($variantInfo['combination']))->toArray()))
            );
            unset($variantInfo['combination']);
            $mapVariantsInfo[$variantId] = $variantInfo;
        }

        $dataVariants = ['my_product_variants_info' => $mapVariantsInfo];
        $dataMyProduct = (new Manager())
            ->setSerializer(new ApiSerializer())
            ->createData(new Item($myProduct, new MyProductTransformer(), 'my_product'))
            ->toArray()
        ;

        return $this->success($dataVariants + $dataMyProduct);
    }

    /**
     * @OA\Get(path="/api/my-products",
     *   tags={"MyProducts"},
     *   summary="Get list of MyProducts",
     *   description="Get list of MyProducts",
     *   @OA\Parameter(
     *      @OA\Schema(type="string"),
     *      parameter="product_status",
     *      name="product_status",
     *      description="Product status. Example: connected | non_connected",
     *      in="query",
     *      required=false
     *   ),
     *   @OA\Response(response="200", description="The MyProducts' list was successfully extracted",
     *      @OA\JsonContent(
     *         @OA\Property(property="my_products", type="array", @OA\Items(
     *            allOf={@OA\Schema(ref="#/components/schemas/Pagination")},
     *               @OA\Property(property="data",ref="#/components/schemas/MyProduct"),
     *            )
     *         ),
     *         @OA\Property(property="stats", type="array", @OA\Items(ref="#/components/schemas/MyProductStats")),
     *         @OA\Property(property="pagination", type="array", @OA\Items(ref="#/components/schemas/Pagination"))
     *      )
     *   ),
     *   @OA\Response(response="400", description="Can't get MyProducts",
     *      @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *   )
     * )
     */
    public function list(MyProductFilter $myProductFilter): JsonResponse
    {
        $perPage = Input::get('per_page', parent::DEFAULT_PER_PAGE);

        $myProducts = MyProduct
            ::with(['shopifyProduct'])
            ->where(['user_id' => auth()->id()])
            ->filter($myProductFilter)
        ;

        $myProducts = $myProducts->paginate($perPage);

        /** @noinspection PhpUndefinedMethodInspection */
        $collection = $myProducts->getCollection();
        $resource = new Collection($collection, new MyProductTransformer(), 'my_products');

        $resource->setPaginator(new IlluminatePaginatorAdapter($myProducts));
        $connectCount = MyProduct::where([
            'user_id' => auth()->id(),
            'status' => MyProductStatusType::CONNECTED
        ])->count();
        $nonConnectedCount = MyProduct::where(['user_id' => auth()->id()])
            ->where('status', '<>', MyProductStatusType::CONNECTED)
            ->count()
        ;

        $resource->setMetaValue('stats', [
            'total' => $connectCount + $nonConnectedCount,
            'connected' => $connectCount,
            'non_connected' => $nonConnectedCount
        ]);

        //$resource->setMetaValue('shop', Shop::where(['user_id' => auth()->id()])->first()->shop);

        return $this->success($resource);
    }
    /**
     * @OA\Get(path="/api/my-products/count",
     *   tags={"MyProducts"},
     *   summary="Get counts of MyProduct",
     *   description="Get counts of MyProduct (connected and non-connected)",
     *   @OA\Response(response="200", description="Count of MyProduct's",
     *      @OA\JsonContent(
     *         allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *      )
     *   )
     * )
     */
    public function count()
    {
        $connectCount = MyProduct::where([
            'user_id' => auth()->id(),
            'status' => MyProductStatusType::CONNECTED
        ])->count();

        $nonConnectedCount = MyProduct::where(['user_id' => auth()->id()])
            ->where('status', '<>', MyProductStatusType::CONNECTED)
            ->count() ;

        return $this->success([
            'my_products_count'=>[
                'total' => $connectCount + $nonConnectedCount,
                'connected' => $connectCount,
                'non_connected' => $nonConnectedCount
            ]
        ]);
    }

    /**
     * @OA\Get(path="/api/my-products/{my_product_id}/images",
     *   tags={"MyProducts"},
     *   summary="Get all images of MyProduct",
     *   description="Get all images of MyProduct by **`my_product_id`**",
     *   @OA\Parameter(ref="#/components/parameters/my_product_id"),
     *   @OA\Response(response="200", description="The MyProduct's images list was successfully extracted",
     *      @OA\JsonContent(
     *         allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *         @OA\Property(property="images", type="array", @OA\Items(ref="#/components/schemas/MyProductImages"))
     *      )
     *   ),
     *   @OA\Response(response="404", description="The MyProduct was not found",
     *      @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *   )
     * )
     */
    public function getMyProductImages(int $id): JsonResponse
    {
        try {
            MyProduct::findOrFail($id);
        } catch (\Throwable $t) {
            abort(
                Response::HTTP_NOT_FOUND,
                sprintf('MyProduct is not found (#%d)', $id)
            );
        }

        return $this->success(['images' => json_decode(MyProduct::findOrFail($id)->images)]);
    }

    /**
     * @OA\Get(path="/api/my-products/{my_product_id}/options/images",
     *   tags={"MyProducts"},
     *   summary="Get all images of all MyProductOptions",
     *   description="Get all images of all MyProductOptions by **`my_product_id`**",
     *   @OA\Parameter(ref="#/components/parameters/my_product_id"),
     *   @OA\Response(response="200", description="The MyProduct's options' images list was successfully extracted",
     *      @OA\JsonContent(
     *         allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *            @OA\Property(property="images", type="array", @OA\Items(ref="#/components/schemas/MyProductImages"))
     *      )
     *   ),
     *   @OA\Response(response="404", description="The MyProduct was not found",
     *      @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *   )
     * )
     */
    public function getOptionsImages(MyProduct $myProduct): JsonResponse
    {
        $productOptionsImages = $myProduct->options ? json_decode($myProduct->options->pluck('image')) : [];

        return $this->success(['images' => array_diff($productOptionsImages, [''])]);
    }

    /**
     * @OA\Get(path="/api/my-products/{my_product_id}/variants",
     *   tags={"MyProducts"},
     *   summary="Get amount and price of MyProductVariants",
     *   description="Get info about amount and price of MyProductVariants by **`my_product_id`**",
     *   @OA\Parameter(ref="#/components/parameters/my_product_id"),
     *   @OA\Response(response="200", description="The MyProduct's variants' info was successfully extracted",
     *      @OA\JsonContent(
     *         allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *         @OA\Property(property="product_variants_info", @OA\Items(ref="#/components/schemas/MyVariantsInfo"))
     *      )
     *   ),
     *   @OA\Response(response="404", description="The MyProduct was not found",
     *      @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *   )
     * )
     */
    public function getVariants(MyProduct $myProduct): JsonResponse
    {
        $variantsInfo = MyProductVariant::select(['combination', 'id', 'price', 'amount'])
            ->where('my_product_id', $myProduct->id)
            ->get()
            ->toArray()
        ;

        $mapVariantsInfo = [];
        foreach ($variantsInfo as $variantInfo) {
            $variantId = implode(
                '-',
                array_sort(array_values(collect(json_decode($variantInfo['combination']))->toArray()))
            );

            unset($variantInfo['combination']);
            $mapVariantsInfo[$variantId] = $variantInfo;
        }

        return $this->success(['my_product_variants_info' => $mapVariantsInfo]);
    }

    /**
     * @OA\Put(
     *   tags={"MyProducts"},
     *   path="/api/my-products/{my_product_id}",
     *   summary="Edit one MyProduct",
     *   description="Edit one MyProduct by **`my_product_id`**, sending only changed fields.",
     *   @OA\Parameter(ref="#/components/parameters/my_product_id"),
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(ref="#/components/schemas/MyProductVariantChangeable")
     *      )
     *   ),
     *   @OA\Response(response="200", description="The MyProduct was successfully edited",
     *      @OA\JsonContent(
     *         allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *         @OA\Property(property="my_product", type="array", @OA\Items(ref="#/components/schemas/MyProduct")),
     *         @OA\Property(property="message", type="string")
     *      )
     *   ),
     *   @OA\Response(response="404", description="The MyProduct was not found",
     *      @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *   )
     * )
     */
    public function store(MyProduct $myProduct): JsonResponse
    {
        /** @noinspection PhpUndefinedMethodInspection */
        request()->validate(['title' => ['required']]);

        $input = request()->only([
            'title',
            'status',
            'price',
            'amount',
            'description',
            'ali_id',
            'image',
            'images',
            'type',
            'vendor',
        ]);
        $myProduct->update($input);
        $myProduct->save();

        // todo: removal via collection!
        if (request()->has(['collections']) && $collections = request()->get('collections')) {
            $collections = $this->handleCollections($collections);
            $myProduct->collections()->saveMany($collections);
        }

        MyProductTag::where('my_product_id', $myProduct->id)->delete();
        collect(explode(',', request()->get('tags')))->each(function (string $tag) use ($myProduct) {
            if ($tag = trim($tag)) {
                MyProductTag::firstOrCreate([
                    'title' => $tag,
                    'user_id' => auth()->id(),
                    'my_product_id' => $myProduct->id,
                ]);
            }
        });

        return $this->success([
            'my_product' => $myProduct->toArray(),
            'message' => 'The MyProduct was successfully edited'
        ]);
    }

    /** @return MyProductCollection[] */
    private function handleCollections(array $collections): array
    {
        $result = [];
        foreach ($collections as $collection) {
            foreach ($collection as $id => $title) {
                if ($realCollection = $id
                    ? MyProductCollection::where([
                        'id' => $id,
                        'user_id' => auth()->id(),
                    ])->firstOr()
                    : MyProductCollection::create([
                        'type' => ShopifyCollectionType::CUSTOM,
                        'title' => $title,
                        'user_id' => auth()->id(),
                    ])
                ) {
                    $result[] = $realCollection;
                }
                break;
            }
        }

        return $result;
    }

    /**
     * @OA\Put(
     *   tags={"MyProducts"},
     *   path="/api/my-products/{my_product_id}/variants/{variant_id}",
     *   summary="Edit one MyProduct's variant",
     *   description="Edit one MyProduct's variant by **`my_product_id`** and **`variant_id`** , sending only changed fields.",
     *   @OA\Parameter(ref="#/components/parameters/my_product_id"),
     *   @OA\Parameter(ref="#/components/parameters/variant_id"),
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(ref="#/components/schemas/MyProductVariantChangeable")
     *      )
     *   ),
     *   @OA\Response(response="200", description="The MyProduct's variant was successfully edited",
     *      @OA\JsonContent(
     *         allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *         @OA\Property(property="my_product_variant", type="array", @OA\Items(ref="#/components/schemas/MyProductVariants"))
     *      )
     *   ),
     *   @OA\Response(response="404", description="The MyProduct or MyProduct's variant were not found",
     *      @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *   )
     * )
     */
    public function storeVariant(MyProduct $myProduct, int $id, Request $request): JsonResponse
    {
        try {
            /** @var MyProductVariant $myProductVariant */
            $myProductVariant = MyProductVariant::where(['id' => $id, 'my_product_id' => $myProduct->id])->firstOrFail();
        } catch (\Throwable $t) {
            abort(
                Response::HTTP_NOT_FOUND,
                sprintf('MyProductVariant (#%d) is not found for this MyProduct (#%d)', $id, $myProduct->id)
            );
        }

        $input = $request->only(['price','amount']);
        $myProductVariant->update($input);
        $myProductVariant->save();

        return $this->success([
            'message' => 'The MyProduct\'s variant was successfully edited',
            'my_product_variant' => $myProductVariant->toArray()
        ]);
    }

    /**
     * @OA\Put(path="/api/my-products/{my_product_id}/variants",
     *   tags={"MyProducts"},
     *   summary="Edit MyProducts variants",
     *   description="Edit MyProducts variants",
     *   @OA\Parameter(ref="#/components/parameters/my_product_id"),
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(ref="#/components/schemas/MyProductVariantsChangeable")
     *      )
     *   ),
     *   @OA\Response(response="200", description="The MyProduct's variant was successfully edited",
     *      @OA\JsonContent(
     *         allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *         @OA\Property(property="my_product_variants", type="array", @OA\Items(ref="#/components/schemas/MyProductVariants"))
     *      )
     *   ),
     *   @OA\Response(response="404", description="The MyProduct or MyProduct's variants were not found",
     *      @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *   )
     * )
     */
    public function storeVariants(MyProduct $myProduct): JsonResponse
    {
        collect(request()->all())->each(function ($values, $variantId) use ($myProduct) {
            $values = collect($values)->only(['price', 'amount'])->toArray();
            MyProductVariant::where(['my_product_id' => $myProduct->id, 'id' => $variantId])->firstOrFail()->update($values);
        });

        return $this->success([
            'message' => 'The MyProduct\'s variants were successfully edited',
            'my_product_variants' => $myProduct->combinations->toArray()
        ]);
    }

    /**
     * @OA\Delete(path="/api/my-products/{my_product_id}/variants/{variant_id}", operationId="removeMyProductVariant",
     *   tags={"MyProducts"},
     *   summary="Delete MyProduct's variant",
     *   description="Delete MyProduct's variant by **`my_product_id`** and **`variant_id`**",
     *   @OA\Parameter(ref="#/components/parameters/my_product_id"),
     *   @OA\Parameter(ref="#/components/parameters/variant_id"),
     *   @OA\Response(response="200", description="The MyProduct's variant was successfully deleted",
     *      @OA\JsonContent(ref="#/components/schemas/SuccessfulResponse")
     *   ),
     *   @OA\Response(response="404", description="The MyProduct or MyProduct's variant were not found",
     *      @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *   )
     * )
     */
    public function deleteVariant(MyProduct $myProduct, int $id): JsonResponse
    {
        try {
            /** @var MyProductVariant $myProductVariant */
            $myProductVariant = MyProductVariant::where(['id' => $id, 'my_product_id' => $myProduct->id])->firstOrFail();
        } catch (\Throwable $t) {
            abort(
                Response::HTTP_NOT_FOUND,
                sprintf('The MyProduct\'s variant (#%d) was not found for this MyProduct (#%d)', $id, $myProduct->id)
            );
        }

        $myProductVariant->delete();

        return $this->success(['message' => 'The MyProduct\'s variant was successfully deleted']);
    }

    /**
     * @OA\Delete(path="/api/my-products/{my_product_id}/variants", operationId="removeMyProductVariants",
     *   tags={"MyProducts"},
     *   summary="Delete MyProduct's variants",
     *   description="Delete MyProduct's variants by **`my_product_id`** and array of variants' ids",
     *   @OA\Parameter(ref="#/components/parameters/my_product_id"),
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/IntegerId"))
     *      )
     *   ),
     *   @OA\Response(response="200", description="The MyProduct's variants were successfully deleted",
     *      @OA\JsonContent(ref="#/components/schemas/SuccessfulResponse")
     *   ),
     *   @OA\Response(response="404", description="The MyProduct or MyProduct's variants were not found",
     *      @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *   )
     * )
     */
    public function deleteVariants(MyProduct $myProduct): JsonResponse
    {
        MyProductVariant::where('my_product_id', $myProduct->id)
            ->whereIn('id', request())
            ->each(function (MyProductVariant $myProductVariant) {
                $myProductVariant->delete();
            })
        ;

        return $this->success([
            'message' => 'The MyProduct\'s variants were successfully deleted',
            'my_product_variants' => $myProduct->combinations->toArray()
        ]);
    }

    /**
     * @OA\Delete(path="/api/my-products", operationId="removeMyProducts",
     *   tags={"MyProducts"},
     *   summary="Delete MyProducts",
     *   description="Delete MyProducts by array of MyProducts' ids",
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/IntegerId"))
     *      )
     *   ),
     *   @OA\Response(response="200", description="The MyProducts were successfully deleted",
     *      @OA\JsonContent(ref="#/components/schemas/SuccessfulResponse")
     *   ),
     *   @OA\Response(response="404", description="The MyProducts were not found",
     *      @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *   )
     * )
     */
    public function delete(): JsonResponse
    {
        $shop = Shop::where(['user_id' => auth()->id()])->first();

        if (! $shop instanceof Shop) {
            abort(
                Response::HTTP_NOT_FOUND,
                sprintf('Cannot find shop for user (#%d)', auth()->id())
            );
        }

        $message = '';

        collect(request())->each(function ($id) use ($shop, &$message) {
            $myProduct = MyProduct::where(['user_id' => auth()->id()])->findOrFail($id);

            if ($myProduct->shopify_product_id) {
                RemoveProductInShopify::dispatch($shop, $myProduct);
                $message = ' (The MyProducts were already successfully deleted in Shopify by its shopify_product_id)';
            }

            $myProduct->delete();
        });

        return $this->success([
            'message' => 'The MyProducts were successfully deleted' . $message,
            'deleted_my_product_ids' => array_values(request()->all()),
        ]);
    }

    /**
     * @OA\Delete(path="/api/my-products/{my_product_id}/images", operationId="removeMyProductImages",
     *   tags={"MyProducts"},
     *   summary="Delete MyProduct's images",
     *   description="Delete MyProduct's images by **`my_product_id`** and paths of images",
     *   @OA\Parameter(ref="#/components/parameters/my_product_id"),
     *   @OA\Response(response="200", description="The images were successfully deleted",
     *      @OA\JsonContent(ref="#/components/schemas/SuccessfulResponse")
     *   ),
     *   @OA\Response(response="404", description="The MyProduct or MyProduct's variants were not found",
     *      @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *   )
     * )
     */
    public function deleteImages(MyProduct $myProduct): JsonResponse
    {
        collect(request())->each(function ($image) use ($myProduct) {
            if ($image == $myProduct->image) {
                Storage::disk('public')->delete($image);
                $myProduct->update(['image' => '']);
            } else {
                $images = collect(json_decode($myProduct->images));
                if (($key = $images->search($image)) !== false) {
                    //Storage::disk('public')->delete($image); - do not remove physically because the origin product images could also be deleted
                    $images->forget($key);
                    $myProduct->update(['images'=>$images->values()->toJson()]);
                }
            }
        });

        return $this->success('The images were successfully deleted');
    }
    /**
     * @OA\Post(path="/api/my-products/{my_product_id}/image", operationId="storeMyProductImage",
     *   tags={"MyProducts"},
     *   summary="Store MyProduct's image",
     *   description="Store MyProduct's image by **`my_product_id`** and path of image",
     *   @OA\Parameter(ref="#/components/parameters/my_product_id"),
     *   @OA\Response(response="200", description="The images were successfully deleted",
     *      @OA\JsonContent(ref="#/components/schemas/SuccessfulResponse")
     *   ),
     *   @OA\Response(response="404", description="The MyProduct or MyProduct's variants were not found",
     *      @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *   )
     * )
     */
    public function storeImage(MyProduct $myProduct): JsonResponse
    {
        /** @noinspection PhpUndefinedMethodInspection */
        request()->validate(['image' => ['required', 'image']]);
        $images = collect(json_decode($myProduct->images))->filter();
        $images->push(request()->file('image')->store('images/products/' . $myProduct->id, 'public'));
        $myProduct->update(
            ['images' => $images->values()->toJson()]
        );

        return $this->success(['message' => 'The image was uploaded', 'images' => $images->values()]);
    }

    /**
     * @OA\Post(path="/api/my-products/{my_product_id}/images", operationId="storeMyProductImages",
     *   tags={"MyProducts"},
     *   summary="Store MyProduct's images",
     *   description="Store MyProduct's images by **`my_product_id`** and path of images",
     *   @OA\Parameter(ref="#/components/parameters/my_product_id"),
     *   @OA\Response(response="200", description="The images were successfully deleted",
     *      @OA\JsonContent(ref="#/components/schemas/SuccessfulResponse")
     *   ),
     *   @OA\Response(response="404", description="The MyProduct or MyProduct's variants were not found",
     *      @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *   )
     * )
     */
    public function storeImages(MyProduct $myProduct): JsonResponse
    {
        /** @noinspection PhpUndefinedMethodInspection */
        request()->validate(['images.*' => ['required', 'image']]);
        $images = collect(json_decode($myProduct->images))->filter();

        collect(request())->each(function ($image) use (&$images, $myProduct) {
            /** @noinspection PhpUndefinedMethodInspection */
            $images->push($image->store('images/products/' . $myProduct->id, 'public'));
        });

        $myProduct->update(['images' => $images->values()->toJson()]);

        return $this->success(['message' => 'The image was uploaded', 'images' => $images->values()]);
    }
}
