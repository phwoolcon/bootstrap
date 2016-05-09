<?php

return [
    'adapter' => 'Generic',
    'options' => [
        'user_model' => 'Phwoolcon\Model\User',
        'user_fields' => [
            'login_fields' => ['username', 'email', 'mobile'],
            'hash_field' => ['password'],
        ],
        'session_key' => 'front',
        'uid_key' => 'id',
        'security' => [
            'default_hash' => Phalcon\Security::CRYPT_BLOWFISH_Y,
            'work_factor' => 5,
        ],
        'hints' => [
            'invalid_password' => 'Invalid password',
            'invalid_user_credential' => 'Invalid user credential',
        ],
    ],
];
