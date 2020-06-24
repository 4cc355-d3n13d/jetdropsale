<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="MyProductUserChangeable",
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="status", type="string"),
 *     @OA\Property(property="price", type="number"),
 *     @OA\Property(property="amount", type="integer"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="image", type="string"),
 *     @OA\Property(property="images", type="string")
 * )
 */

/**
 * @OA\Schema(
 *     schema="MyProduct",
 *     allOf={@OA\Schema(ref="#/components/schemas/MyProductUserChangeable")},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="ali_id", type="integer"),
 *     @OA\Property(property="category_id", type="integer"),
 *     @OA\Property(property="product_id", type="integer"),
 *     @OA\Property(property="shopify_product_id", type="integer"),
 *     @OA\Property(property="product_categories_id", type="integer"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2005-08-09T18:31:42+03:30"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2005-08-09T18:31:42+03:30")
 * )
 */

/**
 * @OA\Schema(
 *     schema="MyProductOptions",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="my_product_id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="value", type="string"),
 *     @OA\Property(property="image", type="string"),
 *     @OA\Property(property="ali_sku", type="integer"),
 *     @OA\Property(property="ali_option_id", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2005-08-09T18:31:42+03:30"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2005-08-09T18:31:42+03:30")
 * )
 */

/**
 * @OA\Schema(
 *     schema="MyProductVariantChangeable",
 *     @OA\Property(property="price", type="number"),
 *     @OA\Property(property="amount", type="integer"),
 * )
 */

/**
 * @OA\Schema(
 *     schema="MyProductVariantsArrayChangeable",
 *     @OA\Property(property="*", type="array", @OA\Items(ref="#/components/schemas/MyProductVariantChangeable")),
 * )
 */

/**
 * @OA\Schema(
 *     schema="MyProductVariantsChangeable",
 *     @OA\Property(property="my_product_variants", type="array", @OA\Items(ref="#/components/schemas/MyProductVariantsArrayChangeable")),
 * )
 */

/**
 * @OA\Schema(
 *     schema="IntegerId",
 *     type="integer"
 * )
 */

/**
 * @OA\Schema(
 *     schema="MyProductVariants",
 *     allOf={@OA\Schema(ref="#/components/schemas/MyProductVariantChangeable")},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="my_product_id", type="integer"),
 *     @OA\Property(property="sku", type="integer"),
 *     @OA\Property(property="amount", type="integer"),
 *     @OA\Property(property="price", type="integer"),
 *     @OA\Property(property="combination", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2005-08-09T18:31:42+03:30"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2005-08-09T18:31:42+03:30")
 * )
 */

/**
 * @OA\Schema(
 *     schema="MyProductStats",
 *     @OA\Property(property="total", type="integer"),
 *     @OA\Property(property="connected", type="integer"),
 *     @OA\Property(property="non_connected", type="integer"),
 * )
 */

/**
 * @OA\Schema(
 *     schema="MyProductImages",
 * )
 */


/**
 * @OA\Schema(
 *     schema="MyVariantInfo",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="price", type="string"),
 *     @OA\Property(property="amount", type="integer")
 * )
 */

/**
 * @OA\Schema(
 *     schema="MyVariantsInfo",
 *     @OA\Property(type="array", @OA\Items(ref="#/components/schemas/MyVariantInfo"))
 * )
 */

/**
 * @OA\Parameter(
 *     @OA\Schema(type="integer"), parameter="my_product_id", name="my_product_id", in="path", required=true, example="1",
 *     description="MyProduct identifier (reference)"
 * )
 */

/**
 * @OA\Parameter(
 *     @OA\Schema(type="integer"), parameter="option_id", name="option_id", in="path", required=true, example="1",
 *     description="Option identifier (reference)"
 * )
 */

/**
 * @OA\Parameter(
 *     @OA\Schema(type="integer"), parameter="variant_id", name="variant_id", in="path", required=true, example="1",
 *     description="MyProductVariant identifier (reference)"
 * )
 */
