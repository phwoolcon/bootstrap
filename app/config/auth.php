<?php

return [
    'adapter' => 'Phwoolcon\Auth\Adapter\Generic',
    'options' => [
        'user_model' => 'Phwoolcon\Model\User',
        'user_fields' => [
            'login_fields' => ['username', 'email', 'mobile'],
            'password_field' => 'password',
        ],
        'session_key' => 'front',
        'remember_login' => [
            'cookie_key' => 'remember',
            'ttl' => 604800,
        ],
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
            'confirmation_code_ttl' => 604800,
        ],
        'redirect_timeout' => 2,
    ],
];
