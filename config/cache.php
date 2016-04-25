<?php
return [
    'default' => 'file',
    'drivers' => [
        'file' => [
            'adapter' => 'File',
            'options' => [
                'cacheDir' => 'cache',
                'prefix' => 'c.'
            ],
        ],
        'redis' => [
            'adapter' => 'Redis',
            'options' => [
                'host' => '127.0.0.1',
                'port' => 6379,
                'index' => 5,
                'persistent' => true,
                'prefix' => '.'
            ],
        ],
    ],
];
