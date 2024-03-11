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
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;

beforeEach(function () {
    $this->prefix = '/system';
});

test('login and logout test', function () {
    testFailResponse($this->post($this->prefix . '/login', []));
    testFailResponse($this->post($this->prefix . '/login', [
        'username' => 'superAdmin',
    ]));
    testFailResponse($this->post($this->prefix . '/login', [
        'username' => 'superAdmin',
        'password' => Str::random(12),
    ]));
    $result = $this->post($this->prefix . '/login', [
        'username' => $this->username,
        'password' => $this->password,
    ]);
    testSuccessResponse($result);
    expect($result)->toHaveKey('data.token');
    $token = Arr::get($result, 'data.token');
    $result = $this->post($this->prefix . '/logout');
    testSuccessResponse($result);
});

test('getInfo test', function () {
    testSuccessResponse($this->get($this->prefix . '/getInfo'));
});

test('refresh token test', function () {
    $result = $this->post($this->prefix . '/refresh');
    testSuccessResponse($result);
    expect($result)->toHaveKey('data.token');
});

test('getBingBackgroundImage test', function () {
    $result = $this->get($this->prefix . '/getBingBackgroundImage');
    testSuccessResponse($result);
    expect($result)->toHaveKey('data.url');
});
