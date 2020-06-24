<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Product",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="status", type="integer"),
 *     @OA\Property(property="price", type="float"),
 *     @OA\Property(property="amount", type="integer"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="image", type="string"),
 *     @OA\Property(property="images", type="string"),
 *     @OA\Property(property="ali_id", type="integer"),
 *     @OA\Property(property="category_id", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2005-08-09T18:31:42+03:30"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2005-08-09T18:31:42+03:30")
 * )
 */

/**
 * @OA\Schema(
 *     schema="ProductOptions",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="product_id", type="integer"),
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
 *     schema="ProductVariants",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="product_id", type="integer"),
 *     @OA\Property(property="sku", type="integer"),
 *     @OA\Property(property="amount", type="integer"),
 *     @OA\Property(property="price", type="integer"),
 *     @OA\Property(property="combination", type="text"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2005-08-09T18:31:42+03:30"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2005-08-09T18:31:42+03:30")
 * )
 */

/**
 * @OA\Schema(
 *     schema="ProductStats",
 *     @OA\Property(property="total", type="integer"),
 *     @OA\Property(property="connected", type="integer"),
 *     @OA\Property(property="non-connected", type="integer"),
 * )
 */

/**
 * @OA\Schema(
 *     schema="VariantInfo",
 *     @OA\Property(property="price", type="string"),
 *     @OA\Property(property="amount", type="integer")
 * )
 */

/**
 * @OA\Schema(
 *     schema="VariantsInfo",
 *     @OA\Property(type="array", @OA\Items(ref="#/components/schemas/VariantInfo"))
 * )
 */

/**
 * @OA\Schema(
 *     schema="ProductImages",
 * )
 */

/**
 * @OA\Parameter(
 *     @OA\Schema(type="integer"), parameter="product_id", name="product_id", in="path", required=true, example="1",
 *     description="Product identifier (reference)"
 * )
 */
