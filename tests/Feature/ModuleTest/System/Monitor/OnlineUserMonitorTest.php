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
use Hyperf\Redis\Redis;
use Hyperf\Snowflake\IdGenerator;

beforeEach(function () {
    $this->prefix = '/system/onlineUser';
});

test('online user controller test', function () {
    testSuccessResponse($this->get($this->prefix . '/index'));
    /**
     * @var IdGenerator $idGenerator
     */
    $idGenerator = make(IdGenerator::class);
    $redis = make(Redis::class);
    $id = $idGenerator->generate();
    $key = sprintf('%sToken:%s', config('cache.default.prefix'), $id);
    $redis->set($key, '123456');
    testSuccessResponse($this->post($this->prefix . '/kick', compact('id')));
});
