<?php

return [
    'default' => 'native',
    'options' => [
        'lifetime' => 86400,
        'cookie_name' => 'phwoolcon',
        'cookie_path' => '/',
        'cookie_domain' => null,
        'cookie_secure' => false,
        'cookie_http_only' => true,
    ],
    'drivers' => [
        'native' => [
            'adapter' => 'Native',
            'options' => [
                'save_path' => storagePath('session'),
            ],
        ],
        'redis' => [
            'adapter' => 'Redis',
            'options' => [
            ],
        ],
        'memcached' => [
            'adapter' => 'Memcached',
            'options' => [
            ],
        ],
    ],
];
