<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Collection extends ResourceCollection
{
    public function __construct($resource, $wrap = null)
    {
        parent::__construct($resource);
        Resource::wrap($wrap);
    }

    public function with($request): array
    {
        return ['result' => 'ok'];
    }
}
