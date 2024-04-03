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
    expect($this->get($this->prefix . '/getUserList'))->toBeHttpSuccess();
});

test('getUserInfoByIds test', function () {
    expect($this->post($this->prefix . '/getUserInfoByIds', ['ids' => [1, 2, 3]]))->toBeHttpSuccess();
});

test('getDeptTreeList test', function () {
    expect($this->get($this->prefix . '/getDeptTreeList'))->toBeHttpSuccess();
});

test('getRoleList test', function () {
    expect($this->get($this->prefix . '/getRoleList'))->toBeHttpSuccess();
});

test('getPostList test', function () {
    expect($this->get($this->prefix . '/getPostList'))->toBeHttpSuccess();
});

test('getOperationLogList test', function () {
    expect($this->get($this->prefix . '/getOperationLogList'))->toBeHttpSuccess();
});

test('getLoginLogList test', function () {
    expect($this->get($this->prefix . '/getLoginLogList'))->toBeHttpSuccess();
});

test('getResourceList test', function () {
    expect($this->get($this->prefix . '/getResourceList'))->toBeHttpSuccess();
});

test('getNoticeList test', function () {
    expect($this->get($this->prefix . '/getNoticeList'))->toBeHttpSuccess();
});

test('clearAllCache test', function () {
    expect($this->get($this->prefix . '/clearAllCache'))->toBeHttpSuccess();
});
