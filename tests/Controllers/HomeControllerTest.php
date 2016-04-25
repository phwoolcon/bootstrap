<?php

namespace Tests\Controllers;

use Tests\ControllerTestCase;

class HomeControllerTest extends ControllerTestCase
{

    public function testHelloWorld()
    {
        $response = $this->request('GET', '/');
        $this->assertContains('hello', $response->getContent());
    }
}
