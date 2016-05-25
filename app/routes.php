<?php
/* @var Phwoolcon\Router $this */

$this->prefix('/api', [
    'GET' => [
        '/' => function () {
            return Phalcon\Di::getDefault()
                ->getShared('response')
                ->setJsonContent(['content' => 'Phwoolcon Bootstrap'])
                ->setHeader('Content-Type', 'application/json');
        },
    ],
], MultiFilter::instance()
    ->add(DisableSessionFilter::instance())
    ->add(DisableCsrfFilter::instance())
)->prefix('/admin', [
    'GET' => [
        '/:params' => 'Admin\Controllers\AccountController::missingMethod',
        '/' => 'Admin\Controllers\AccountController::getIndex',
        '/login' => 'Admin\Controllers\AccountController::getLogin',
    ],
    'POST' => [
        '/login' => 'Admin\Controllers\AccountController::postLogin',
    ],
])->prefix('/account', [
    'GET' => [
        '/' => 'Auth\Controllers\AccountController::getIndex',
        '/login' => 'Auth\Controllers\AccountController::getLogin',
        '/register' => 'Auth\Controllers\AccountController::getRegister',
        '/logout' => 'Auth\Controllers\AccountController::getLogout',
        '/redirect' => 'Auth\Controllers\AccountController::getRedirect',
        '/confirm' => 'Auth\Controllers\AccountController::getConfirm',
        '/activate' => 'Auth\Controllers\AccountController::getActivate',
        '/forgot-password' => 'Auth\Controllers\AccountController::getForgotPassword',
    ],
    'POST' => [
        '/login' => 'Auth\Controllers\AccountController::postLogin',
        '/register' => 'Auth\Controllers\AccountController::postRegister',
        '/forgot-password' => 'Auth\Controllers\AccountController::postForgotPassword',
    ],
])->prefix('/sso', [
    'GET' => [
        'check-iframe' => 'Auth\Controllers\SsoController::getCheckIframe',
    ],
]);

return [
    'GET' => [
        '/' => function () {
            return View::make('', 'index', ['page_title' => 'Phwoolcon']);
        },
        'terms' => function () {
            return View::make('', 'terms', ['page_title' => __('Terms of Service') . ' - Phwoolcon']);
        },
        'about-us' => function () {
            return View::make('', 'about-us', ['page_title' => __('About Us') . ' - Phwoolcon']);
        },
    ],
];
