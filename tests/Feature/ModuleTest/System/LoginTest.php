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
    expect($this->post($this->prefix . '/login', []))->toBeHttpFail()
        ->and($this->post($this->prefix . '/login', [
            'username' => 'superAdmin',
        ]))->toBeHttpFail()
        ->and($this->post($this->prefix . '/login', [
            'username' => 'superAdmin',
            'password' => Str::random(12),
        ]))->toBeHttpFail();
    $result = $this->post($this->prefix . '/login', [
        'username' => $this->username,
        'password' => $this->password,
    ]);
    expect($result)->toBeHttpSuccess()
        ->and($result)->toHaveKey('data.token');
    $token = Arr::get($result, 'data.token');
    $result = $this->post($this->prefix . '/logout');
    expect($result)->toBeHttpSuccess();
});

test('getInfo test', function () {
    expect($this->get($this->prefix . '/getInfo'))->toBeHttpSuccess();
});

test('refresh token test', function () {
    $result = $this->post($this->prefix . '/refresh');
    expect($result)->toBeHttpSuccess()
        ->and($result)->toHaveKey('data.token');
});

test('getBingBackgroundImage test', function () {
    $result = $this->get($this->prefix . '/getBingBackgroundImage');
    expect($result)->toBeHttpSuccess()
        ->and($result)->toHaveKey('data.url');
});
