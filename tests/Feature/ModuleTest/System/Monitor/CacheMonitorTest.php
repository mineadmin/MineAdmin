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
    expect($this->get($this->prefix . '/monitor'))->toBeHttpSuccess();
});

test('view test', function () {
    expect($this->post($this->prefix . '/view', [
        'key' => 'cache1',
    ]))->toBeHttpSuccess();
});

test('delete test', function () {
    expect($this->delete($this->prefix . '/delete', [
        'key' => 'cache1',
    ]))->toBeHttpSuccess();
});

test('clear test', function () {
    expect($this->delete($this->prefix . '/clear'))->toBeHttpSuccess();
});
