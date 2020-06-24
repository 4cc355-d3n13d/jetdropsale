<?php

namespace App\Models;

use ScoutElastic\IndexConfigurator;

class OrderIndexConfigurator extends IndexConfigurator
{
    protected $name = 'order_index';

    protected $settings = [
        'analysis' => [
            'analyzer' => [
                'my_analyzer' => [
                    'type' => 'custom',
                    'tokenizer' => 'standard',
                    'char_filter' => [
                        'html_strip',
                    ]
                ],
                'my_email_analyzer' => [
                    'type' => 'custom',
                    'tokenizer' => 'uax_url_email',
                ],
            ]
        ]
    ];

    protected $defaultMapping = [
        'dynamic_templates' => [
            [
                'zipcode' => [
                    'match' => 'zipcode',
                    'mapping' => [
                        'type' => 'text',
                    ]
                ]
            ]
        ]
    ];
}
