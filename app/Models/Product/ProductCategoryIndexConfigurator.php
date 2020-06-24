<?php

namespace App\Models\Product;

use ScoutElastic\IndexConfigurator;

class ProductCategoryIndexConfigurator extends IndexConfigurator
{
    protected $name = 'product_category_index';

    protected $settings = [
        'analysis' => [
            'analyzer' => [
                'my_analyzer' => [
                    'tokenizer' => 'keyword',
                    'char_filter' => [
                        0 => 'html_filter',
                    ],
                ]
            ],
            'char_filter' => [
                'html_filter' => [
                    'type' => 'html_strip',
                    'escaped_tags' => [
                        0 => 'b',
                    ],
                ],
            ],
        ]
    ];
}
