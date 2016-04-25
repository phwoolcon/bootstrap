<?php

namespace Tests;

use Phalcon\Http\Response;

class ControllerTestCase extends TestCase
{

    public function request($method, $url, $params = [], $headers = [])
    {
        return new Response('hello world');
    }
}
