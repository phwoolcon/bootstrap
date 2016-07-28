<?php
/* @var Phwoolcon\Router $this */

$this->prefix('/api', [
    'GET' => [
        '/:params' => 'Payment\Controllers\Api\AlipayController::missingMethod',
        '/' => function () {
            return Phalcon\Di::getDefault()
                ->getShared('response')
                ->setJsonContent(['content' => 'Phwoolcon Bootstrap'])
                ->setHeader('Content-Type', 'application/json');
        },
    ],
    'POST' => [
        '/alipay/pay-request' => 'Payment\Controllers\Api\AlipayController::postRequest',
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
        '/check' => 'Auth\Controllers\SsoController::getCheckIframe',
        '/redirect' => 'Auth\Controllers\SsoController::getRedirect',
    ],
    'POST' => [
        '/server-check' => [
            'Auth\Controllers\SsoController::postServerCheck',
            'filter' => DisableCsrfFilter::instance(),
        ],
    ],
])->prefix('/pay', [
    'GET' => [
        '/form' => 'Payment\Controllers\OrderController::getForm',
        '/demo-request-form' => 'Payment\Controllers\OrderController::getDemoRequestForm',
    ],
    'POST' => [
        '/order/place' => 'Payment\Controllers\OrderController::postPlace',
    ],
])->prefix('/catalog', [
    'GET' => [
        '/' => function () {
            return View::make('catalog', 'index', ['page_title' => __('Catalog') . ' - Phwoolcon']);
        },
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
