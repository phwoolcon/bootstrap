<?php

return [
    'enable' => true,
    'default' => 'files',
    'drivers' => [
        'files' => [
        ],
        'redis' => [
            'host' => 'localhost',
            'port' => 6379,
//            'auth' => '',
            'persistent' => false,
            'lifetime' => 3600,
            'prefix' => 'my_',
        ],
        'memcache' => [
            'host' => '127.0.0.1',
            'port' => 11211,
            'persistent' => true,
            'lifetime' => 3600,
            'prefix' => 'my_',
        ],
        'memcached' => class_exists('Memcached') ? [
            'adapter' => 'Libmemcached',
            'servers' => [
                ['host' => 'localhost', 'port' => 11211, 'weight' => 1],
            ],
            'client' => [
                Memcached::OPT_HASH => Memcached::HASH_MD5,
                Memcached::OPT_PREFIX_KEY => 'prefix.',
            ],
            'lifetime' => 3600,
            'prefix' => 'my_',
        ] : [],
    ],
];
