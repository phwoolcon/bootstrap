<?php

use Phwoolcon\Exception\HttpException;
use Phwoolcon\Log;

function sendHttpStatus($code)
{
    $statuses = [
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        226 => 'IM Used',
        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy', // Deprecated
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        499 => 'Client Closed Request',
        // SERVER ERROR
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];
    if (isset($statuses[$code])) {
        header('HTTP/1.1 ' . $code . ' ' . $statuses[$code]);
        header('Status:' . $code . ' ' . $statuses[$code]);
    }
}

/**
 * @param Throwable $exception
 */
function exceptionHandler($exception)
{
    profilerStop();
    try {
        Log::exception($exception);
    } catch (Exception $e) {
    }
    if ($exception instanceof HttpException) {
        $response = $exception->toResponse();
        $response->send();
        return;
    }
    if (isset($_SERVER['PHWOOLCON_EXCEPTION_HANDLER'])) {
        call_user_func($_SERVER['PHWOOLCON_EXCEPTION_HANDLER'], $exception);
        return;
    }
    sendHttpStatus(500);
    header('content-type: application/json');
    echo json_encode([
        'error_code' => 500,
        'error_msg' => 'Server down, please check log!',
    ]);
}

function errorHandler($errNo, $errStr, $errFile, $errLine)
{
    $levels = [
        E_WARNING => 'Warning',
        E_NOTICE => 'Notice',
        E_STRICT => 'Strict',
        E_DEPRECATED => 'Deprecated',
    ];
    $errLevel = $errNo;
    isset($levels[$errNo]) and $errLevel = $levels[$errNo];
    $exception = new ErrorException($errLevel . ' - ' . $errStr, $errNo, 1, $errFile, $errLine);
    if (empty($_SERVER['PHWOOLCON_CONTINUE_ON_ERROR'])) {
        throw $exception;
    }
    try {
        Log::exception($exception);
    } catch (Exception $e) {
    }
}

function profilerStart()
{
    if (isset($_SERVER['ENABLE_PROFILER']) && function_exists('xhprof_enable')) {
        xhprof_enable(0, [
            'ignored_functions' => [
                'call_user_func',
                'call_user_func_array',
            ],
        ]);
    }
}

function profilerStop($type = 'fpm')
{
    if (isset($_SERVER['ENABLE_PROFILER']) && function_exists('xhprof_enable')) {
        static $profiler;
        static $profilerDir;
        $data = xhprof_disable();
        $profilerDir || is_dir($profilerDir = storagePath('profiler')) or mkdir($profilerDir, 0777, true);
        $pathInfo = strtr($_SERVER['REQUEST_URI'], ['/' => '~', '?' => '@', '&' => '!']);
        $microTime = explode(' ', microtime());
        $reportFile = $microTime[1] . '-' . substr($microTime[0], 2) . '-' . $_SERVER['REQUEST_METHOD'] . $pathInfo;
        $profiler or $profiler = new XHProfRuns_Default($profilerDir);
        $profiler->save_run($data, $type, $reportFile);
    }
}
