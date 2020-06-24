<?php

namespace App\Http\Resources;

use App\Models\Product\MyProductTag;

class TagItem extends Item
{
    public function toArray($request)
    {
        /** @var MyProductTag $this */
        return [
            (int) $this->id => (string) $this->title,
        ];
    }
}
