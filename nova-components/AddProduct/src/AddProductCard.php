<?php

namespace Dropwow\AddProduct;

use Laravel\Nova\Card;

class AddProductCard extends Card
{
    /**
     * The width of the card (1/3, 1/2, or full).
     */
    public $width = '1/3';

    /**
     * Get the component name for the element.
     * @return string
     */
    public function component()
    {
        return 'add_product';
    }
}
