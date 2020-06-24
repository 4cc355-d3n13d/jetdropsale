<?php

namespace App\Http\Controllers\Api\Internal;

class ImageController extends ApiController
{
    // @todo
    /**
     * @OA\Get(
     *     tags={"Image"},
     *     path="/api/image/{image_id}",
     *     @OA\Parameter(ref="#/components/parameters/image_id"),
     *     description="Получение одного изображения по его id",
     *     @OA\Response(
     *         response="default",
     *         description="successful operation"
     *     )
     * )
     */

    /**
     * @OA\Delete(
     *     tags={"Image"},
     *     path="/api/image/{image_id}",
     *     description="Удаление изображения",
     *     @OA\Parameter(ref="#/components/parameters/image_id"),
     *     @OA\Response(
     *         response="default",
     *         description="successful operation"
     *     )
     * )
     */

    /**
     * @OA\Parameter(
     *     parameter="image_id",
     *     name="image_id",
     *     in="path",
     *     description="Image id",
     *     required=true,
     *     @OA\Schema(
     *         type="integer",
     *     )
     * )
     */
}
