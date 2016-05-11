<?php

return [
    'adapter' => 'Generic',
    'options' => [
        'user_model' => 'Phwoolcon\Model\User',
        'user_fields' => [
            'login_fields' => ['username', 'email', 'mobile'],
            'password_field' => 'password',
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
            'user_credential_registered' => 'User credential registered',
            'unable_to_save_user' => 'Unable to save user',
        ],
        'register' => [
            'confirm_mobile' => false,
            'confirm_email' => false,
        ],
        'redirect_timeout' => 2,
    ],
];
