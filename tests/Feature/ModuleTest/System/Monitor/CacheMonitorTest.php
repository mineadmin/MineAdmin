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
use Hyperf\Context\ApplicationContext;
use Hyperf\Redis\Redis;

beforeEach(function () {
    $this->prefix = '/system/cache';
    $redis = ApplicationContext::getContainer()
        ->get(Redis::class);
    $redis->set('cache1', 1);
    $redis->expire('cache1', 100);
});

test('get monitor test', function () {
    testSuccessResponse($this->get($this->prefix . '/monitor'));
});

test('view test', function () {
    testSuccessResponse($this->post($this->prefix . '/view', [
        'key' => 'cache1',
    ]));
});

test('delete test', function () {
    testSuccessResponse($this->delete($this->prefix . '/delete', [
        'key' => 'cache1',
    ]));
});

test('clear test', function () {
    testSuccessResponse($this->delete($this->prefix . '/clear'));
});
