<?php
return [
    'GET' => [
        '/admin/:params' => 'Admin\Controllers\AccountController::missingMethod',
        '/' => function () {
            return '<!DOCTYPE html><html><head><title>Phwoolcon Bootstrap</title></head><body><h1 style="margin:100px 0;text-align:center;">Welcome to Phwoolcon</h1></body></html>';
        },
        '/admin' => 'Admin\Controllers\AccountController::getIndex',
        '/admin/login' => 'Admin\Controllers\AccountController::getLogin',
    ],
    'POST' => [
        'admin/login' => 'Admin\Controllers\AccountController::postLogin',
    ],
];
