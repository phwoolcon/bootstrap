<?php

return [
    'default' => 'default_queue',
    'queues' => [
        'default_queue' => [
            'connection' => 'beanstalkd',
            'options' => [],
        ],
    ],
    'connections' => [
        'beanstalkd' => [
            'adapter' => 'Beanstalkd',
            'host' => '127.0.0.1',
            'port' => 11300,
            'connect_timeout' => 5,
            'persistence' => false,
            'default' => 'default',
        ],
        'file' => [
            'path' => ROOT_PATH . '/storage/queue',
            'ext' => '.data',
        ],
    ],
];
