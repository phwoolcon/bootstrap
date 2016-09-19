<?php

if (!empty($_SERVER['DEV_MODE']) && !empty($_REQUEST['fpm'])) {
    return;
}

$config = is_file($configFile = dirname(__DIR__) . '/app/config/production/service.php') ? include($configFile) : [];
$runDir = isset($config['run_dir']) ? $config['run_dir'] : '/tmp/phwoolcon/';

if (!is_file($portFile = $runDir . 'service-port.php')) {
    return;
}

$port = include($portFile);

$sockFile = $runDir . 'service-' . $port . '.sock';
$client = new swoole_client(SWOOLE_UNIX_STREAM);
$client->set([
    'open_length_check' => true,
    'package_max_length' => 262144,
    'package_length_type' => 'N',
    'package_length_offset' => 0,
    'package_body_offset' => 4,
]);

if (!@$client->connect($sockFile, 0, 20)) {
    return;
}

$request = [
    'request' => $_REQUEST,
    'cookies' => $_COOKIE,
    'server' => $_SERVER,
    'files' => $_FILES,
];

$request = serialize($request);
$request = pack('N', $length = strlen($request)) . $request;

if ($length > 2097152) {
    foreach (str_split($request, 1048576) as $chunk) {
        $client->send($chunk);
    }
} else {
    $client->send($request);
}
$response = $client->recv();
$client->close();

if ($response === false) {
    header('Bad Gateway', true, 502);
    echo '<html><head><title>502 Bad Gateway</title></head><body bgcolor="white"><center><h1>502 Bad Gateway</h1></center><hr><center>nginx</center><div style="display:none">err ' . $client->errCode . ': ' . swoole_strerror($client->errCode) . '</div></body></html>';
    exit;
}

$length = unpack('N', $response)[1];
$response = unserialize(substr($response, -$length));

if (isset($response['headers']) && $headers = $response['headers']) {
    isset($headers['status']) and header($headers['status'], true);
    if (isset($headers['set_cookies']) && is_array($headers['set_cookies'])) {
        foreach ($headers['set_cookies'] as $cookie) {
            call_user_func_array('setcookie', $cookie);
        }
    }
    if (isset($headers['headers']) && is_array($headers['headers'])) {
        foreach ($headers['headers'] as $v) {
            header($v, false);
        }
    }
}
if (isset($response['meta']) && is_array($response['meta'])) {
    foreach ($response['meta'] as $k => $v) {
        header('X-Meta-' . $k . ': ' . $v);
    }
}
echo $response['body'];
exit;
