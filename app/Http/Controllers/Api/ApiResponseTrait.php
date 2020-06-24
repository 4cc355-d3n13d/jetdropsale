<?php

namespace App\Http\Controllers\Api;

use App\ApiSerializer;
use Illuminate\Http\JsonResponse;
use League\Fractal\Manager;
use League\Fractal\Resource\ResourceAbstract;

trait ApiResponseTrait
{
    /**
     * @param ResourceAbstract|array|string $data
     * @param int $code
     * @param array $headers
     * @param bool $prettyPrint
     * @return JsonResponse
     */
    protected function success(
        $data = null,
        int $code = JsonResponse::HTTP_OK,
        array $headers = [],
        bool $prettyPrint = true
    ): JsonResponse {
        $status = ['result' => 'ok'];

        if ($data) {
            if ($data instanceof ResourceAbstract) {
                $manager = (new Manager())->setSerializer(new ApiSerializer());
                $data = $manager->createData($data)->toArray();
            } elseif (is_string($data)) {
                $data = array_merge($status, ['message' => $data]);
            }
        }

        return response()->json(
            $data ? array_merge($status, $data) : $status,
            $code,
            $headers,
            $prettyPrint ? JSON_PRETTY_PRINT : 0
        );
    }
}
