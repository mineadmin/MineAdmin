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
    $this->prefix = '/system/onlineUser';
});

test('online user controller test', function () {
    testSuccessResponse($this->get($this->prefix . '/index'));
    $id = $this->mock->id;
    testSuccessResponse($this->post($this->prefix . '/kick', compact('id')));
});
