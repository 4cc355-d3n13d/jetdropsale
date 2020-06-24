<?php

namespace App\Services;

use Http\Client\Common\HttpMethodsClient;
use Illuminate\Support\Facades\App;

/**
 * Class AliProductService
 */
class AliProductService
{
    public function fetchById(string $productCode)
    {
        App::make(HttpMethodsClient::class)->post(
            env('ALI_PARSER_ENDPOINT'), [], '{"productCode": ' . $productCode . '}'
        );
    }
}
