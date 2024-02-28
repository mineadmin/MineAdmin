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
    $this->prefix = '/system/common';
});

test('getUserList test', function () {
    testSuccessResponse($this->get($this->prefix . '/getUserList'));
});

test('getUserInfoByIds test', function () {
    testSuccessResponse($this->post($this->prefix . '/getUserInfoByIds', ['ids' => [1, 2, 3]]));
});

test('getDeptTreeList test', function () {
    testSuccessResponse($this->get($this->prefix . '/getDeptTreeList'));
});

test('getRoleList test', function () {
    testSuccessResponse($this->get($this->prefix . '/getRoleList'));
});

test('getPostList test', function () {
    testSuccessResponse($this->get($this->prefix . '/getPostList'));
});

test('getOperationLogList test', function () {
    testSuccessResponse($this->get($this->prefix . '/getOperationLogList'));
});

test('getLoginLogList test', function () {
    testSuccessResponse($this->get($this->prefix . '/getLoginLogList'));
});

test('getResourceList test', function () {
    testSuccessResponse($this->get($this->prefix . '/getResourceList'));
});

test('getNoticeList test', function () {
    testSuccessResponse($this->get($this->prefix . '/getNoticeList'));
});

test('clearAllCache test', function () {
    testSuccessResponse($this->get($this->prefix . '/clearAllCache'));
});
