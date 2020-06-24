<?php

namespace App\Models;

use ScoutElastic\IndexConfigurator;

class UserIndexConfigurator extends IndexConfigurator
{
    protected $name = 'user_index';

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
