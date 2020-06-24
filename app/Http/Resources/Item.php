<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\Resource;

class Item extends JsonResource
{
    public function __construct($resource, $wrap = null)
    {
        Resource::wrap("");
        if (is_string($wrap)) {
            Resource::wrap($wrap);
        }
        parent::__construct($resource);
    }

    public function with($request): array
    {
        return ['result' => 'ok'];
    }
}
