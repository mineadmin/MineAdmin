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
    $this->prefix = '/setting/datasource';
});

test('index test', function () {
    $result = $this->get($this->prefix . '/index');
    testSuccessResponse($result);
});

test('save test', function () {
    testFailResponse($this->post($this->prefix . '/save', []));
    testFailResponse($this->post($this->prefix . '/save', [
        'source_name' => 'test',
    ]));
    testFailResponse($this->post($this->prefix . '/save', [
        'source_name' => 'test',
        'dsn' => 'test',
        'username' => 'root',
    ]));
    testSuccessResponse($this->post($this->prefix . '/save', [
        'source_name' => 'test',
        'dsn' => 'test',
        'username' => 'root',
        'password' => 'root',
    ]));
});
