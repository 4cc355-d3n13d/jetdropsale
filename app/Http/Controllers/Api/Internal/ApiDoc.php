<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="0.0.1",
 *     title="Dropwow API",
 *     description="Dropwow internal user API for SPA"
 * )
 */

/**
 * @OA\Schema(
 *     schema="SuccessfulResponse",
 *     @OA\Property(property="result", type="string", example="ok")
 * )
 */

/**
 * @OA\Schema(
 *     schema="FailedResponse",
 *     @OA\Property(property="result", type="string", example="error"),
 *     @OA\Property(property="message", type="string", example="Operation was failed")
 * )
 */

/**
 * @OA\Schema(
 *     schema="Pagination",
 *     @OA\Property(property="total", type="integer"),
 *     @OA\Property(property="current_page", type="integer"),
 *     @OA\Property(property="from", type="integer"),
 *     @OA\Property(property="to", type="integer"),
 *     @OA\Property(property="per_page", type="integer"),
 *     @OA\Property(property="last_page", type="integer"),
 *     @OA\Property(property="first_page_url", type="string", format="url"),
 *     @OA\Property(property="next_page_url", type="string", format="url"),
 *     @OA\Property(property="prev_page_url", type="string", format="url"),
 *     @OA\Property(property="last_page_url", type="string", format="url"),
 *     @OA\Property(property="path", type="string", format="url"),
 * )
 */
