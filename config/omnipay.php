<?php

return [

    /** The default gateway name */
    'gateway' => 'Stripe',

    /** The default settings, applied to all gateways */
    'defaults' => [
        'testMode' => false,
    ],

    /** Gateway specific parameters */
    'gateways' => [
        'Stripe' => [
            'apiKey' =>  env('STRIPE_SECRET_KEY'),
            'landingPage' => ['billing', 'login'],
        ],
        'PayPal_Express' => [
            'username' => '',
            'landingPage' => ['billing', 'login'],
        ],
    ],

];
