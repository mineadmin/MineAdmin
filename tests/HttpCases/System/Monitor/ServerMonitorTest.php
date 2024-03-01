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
    $this->prefix = '/system/server';
});

test('monitor test', function () {
    if (swoole_is_in_container()) {
        testFailResponse($this->get($this->prefix . '/monitor'));
    } else {
        testSuccessResponse($this->get($this->prefix . '/monitor'));
    }
});
