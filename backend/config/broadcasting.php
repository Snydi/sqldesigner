<?php

return [
    'default' => env('BROADCAST_CONNECTION', 'null'),

    'connections' => [
        'reverb' => [
            'driver' => 'reverb',
            'key' => env('REVERB_APP_KEY'),
            'secret' => env('REVERB_APP_SECRET'),
            'app_id' => env('REVERB_APP_ID'),
            'options' => [
                'host' => env('REVERB_INTERNAL_HOST', env('REVERB_HOST')),
                'port' => env('REVERB_INTERNAL_PORT', env('REVERB_PORT', 443)),
                'scheme' => env('REVERB_INTERNAL_SCHEME', env('REVERB_SCHEME', 'https')),
                'useTLS' => env('REVERB_INTERNAL_SCHEME', env('REVERB_SCHEME', 'https')) === 'https',
            ],
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],
    ],
];
