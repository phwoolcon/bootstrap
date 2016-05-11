<?php
return [
    'locale_path' => ROOT_PATH . '/app/locale',
    'cache_locale' => true,
    'multi_locale' => false,
    'default_locale' => 'zh_CN',
    'detect_client_locale' => false,
    'verification_patterns' => [
        'CN' => [
            'mobile' => '/^1[34578]\d{9}$/',
            'zip_code' => '/^\d{6}$/',
        ],
    ],
];
