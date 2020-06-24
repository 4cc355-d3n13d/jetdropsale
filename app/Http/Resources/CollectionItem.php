<?php

namespace App\Http\Resources;

use App\Models\Product\MyProductCollection;

class CollectionItem extends Item
{
    public function toArray($request)
    {
        #x =
        /** @var MyProductCollection $this */
        return [
            'id' => $this->id,
            'title' => $this->title
        ];
    }
}
