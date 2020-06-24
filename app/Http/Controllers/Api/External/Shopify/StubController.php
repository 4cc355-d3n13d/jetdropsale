<?php

namespace App\Http\Controllers\Api\External\Shopify;

use App\Http\Controllers\Api\External\ApiController;
use Illuminate\Http\Response;


/**
 * Class StubController
 */
class StubController extends ApiController
{
    public function index()
    {
        return ['status' => Response::HTTP_OK, 'data' => 'shopify hook was found'];
    }
}
