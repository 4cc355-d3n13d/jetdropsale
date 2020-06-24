<?php

namespace App\Http\Controllers\Api\Internal;

use App\Http\Controllers\Api\ApiResponseTrait;
use Illuminate\Routing\Controller as BaseController;

abstract class ApiController extends BaseController
{
    use ApiResponseTrait;

    protected const DEFAULT_PER_PAGE = 16;
}
