<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */
beforeEach(function () {
    $this->prefix = '/system';
});
test('login', function () {
    testFailResponse($this->post($this->prefix . '/login', []));
    testFailResponse($this->post($this->prefix . '/login', [
        'username' => 'superAdmin',
    ]));
    $result = $this->post($this->prefix . '/login', [
        'username' => 'SuperAdmin',
        'password' => 'admin123',
    ]);
    testSuccessResponse($result);
    expect($result)->toHaveKey('data.token');
});
