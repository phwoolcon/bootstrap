<?php

return [
    'default' => 'native',
    'drivers' => [
        'native' => [
            'adapter' => 'Native',
            'options' => [
                'save_path' => storagePath('session'),
                'lifetime' => 86400,
            ],
        ],
        'redis' => [
            'adapter' => 'Redis',
            'options' => [
                'lifetime' => 86400,
            ],
        ],
        'memcached' => [
            'adapter' => 'Memcached',
            'options' => [
                'lifetime' => 86400,
            ],
        ],
    ],
];
