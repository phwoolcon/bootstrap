<?php

return [
    'default' => 'native',
    'options' => [
        /**
         * Cookie lifetime, in seconds.
         */
        'lifetime' => 86400,
        /**
         * CSRF protection token lifetime, in seconds.
         */
        'csrf_token_lifetime' => 3600,
        'cookie_name' => 'phwoolcon',
        'cookie_path' => '/',
        'cookie_domain' => null,
        'cookie_secure' => false,
        'cookie_http_only' => true,
        /**
         * Without lazy renew, the session cookie will be set on every request,
         * which is meaningless to just renew the cookie's expiration time.
         * Let's renew it later, if the lifetime is long enough.
         * Interval in seconds, false to disable.
         */
        'cookie_lazy_renew_interval' => 600,
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
