<?php

use Monolog\Handler\StreamHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [

        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel-' . PHP_SAPI . '.log'),
            'level' => 'debug',
        ],
        'telegram' => [
            'driver' => 'telegram',
            'handler' => \App\Vendor\Monolog\TelegramHandler::class,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel-' . PHP_SAPI . '.log'),
            'level' => 'debug',
            'days' => 7,
        ],

        'shopify' => [
            'driver' => 'monolog',
            'handler' => \Monolog\Handler\ElasticSearchHandler::class,
            'formatter' => \App\Vendor\Monolog\ElasticFormatter::class,
            'formatter_with' => [
                'channel' => 'shopify'
            ]
        ],

        'billing' => [
            'driver' => 'monolog',
            'handler' => \Monolog\Handler\ElasticSearchHandler::class,
            'formatter' => \App\Vendor\Monolog\ElasticFormatter::class,
            'formatter_with' => [
                'channel' => 'billing'
            ],
//            'driver' => 'daily',
//            'path' => storage_path('logs/billing.log'),
//            'level' => 'debug',
//            'days' => 7,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => 'https://hooks.slack.com/services/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',
            'username' => config("BASE_HOST"),
            'emoji' => ':boom:',
          //  'level' => 'critical',
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],
    ],

];
