<?php

namespace App\Models\Product;

use ScoutElastic\IndexConfigurator;

class ProductIndexConfigurator extends IndexConfigurator
{
    protected $name = 'product_index';

    protected $settings = [
        'analysis' => [
            'analyzer' => [
                'my_analyzer' => [
                    'type' => 'custom',
                    'tokenizer' => 'standard',
                    'char_filter' => [
                        'html_strip',
                    ],
                ]
            ]
        ]
    ];
}
