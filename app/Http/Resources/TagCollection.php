<?php

namespace App\Http\Resources;

class TagCollection extends Collection
{
    public $collects = TagItem::class;

    public function __construct($resource)
    {
        parent::__construct($resource, 'tags');
    }
}
