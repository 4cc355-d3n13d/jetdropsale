<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ProductCategory",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="parent_id", type="integer"),
 *     @OA\Property(property="sort", type="integer"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="path", type="string"),
 *     @OA\Property(property="icon", type="string"),
 *     @OA\Property(property="slug", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2005-08-09T18:31:42+03:30"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2005-08-09T18:31:42+03:30")
 * )
 */

/**
 * @OA\Schema(
 *     schema="ProductCategoriesList",
 *     allOf={@OA\Schema(ref="#/components/schemas/ProductCategoryChildren")},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="parent_id", type="integer"),
 *     @OA\Property(property="ali_id", type="integer"),
 *     @OA\Property(property="sort", type="integer"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="ali_title", type="string"),
 *     @OA\Property(property="icon", type="string"),
 *     @OA\Property(property="slug", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2005-08-09T18:31:42+03:30"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2005-08-09T18:31:42+03:30")
 * )
 */

/**
 * @OA\Schema(
 *     schema="ProductCategoryChildren",
 *     @OA\Property(property="children", type="array", @OA\Items(ref="#/components/schemas/ProductCategory")),
 * )
 */
