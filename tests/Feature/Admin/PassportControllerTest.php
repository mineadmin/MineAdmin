<?php

namespace HyperfTests\Feature\Admin;

use HyperfTests\HttpTestCase;
use PHPUnit\Framework\TestCase;

class PassportControllerTest extends HttpTestCase
{
    public function testLogin()
    {
        $result = $this->post('/admin/passport/login', [
            'username' => 'admin',
            'password' => 'admin'
        ]);
        var_dump($result);
    }
}