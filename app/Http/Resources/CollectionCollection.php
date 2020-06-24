<?php

namespace App\Http\Resources;

class CollectionCollection extends Collection
{
    public $collects = CollectionItem::class;

    public function __construct($resource)
    {
        parent::__construct($resource, 'collections');
    }
}
