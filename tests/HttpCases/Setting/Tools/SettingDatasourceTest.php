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
    $saveResult = $this->post($this->prefix . '/save', [
        'source_name' => 'currentData',
        'dsn' => 'test',
        'username' => 'root',
        'password' => 'root',
    ]);
    var_dump($saveResult);
    testSuccessResponse($saveResult);
    expect($saveResult)->toHaveKey('data.id');
    $updateUri = $this->prefix . '/update/' . Arr::get($saveResult, 'data.id');
    testFailResponse($this->put($updateUri, []));
    testFailResponse($this->put($updateUri, [
        'source_name' => 'test',
    ]));
    testFailResponse($this->put($updateUri, [
        'source_name' => 'test',
        'dsn' => 'test',
        'username' => 'root',
    ]));
    $updateResult = $this->put($updateUri, [
        'source_name' => 'test',
        'dsn' => 'test',
        'username' => 'root',
        'password' => 'root',
    ]);
    testSuccessResponse($updateResult);
    $readResult = $this->get($this->prefix . '/read/' . Arr::get($saveResult, 'data.id'));
    testSuccessResponse($readResult);
    expect($readResult['data']['id'])->toEqual(Arr::get($saveResult, 'data.id'));
    testFailResponse($this->delete($this->prefix . '/delete'));
    testSuccessResponse($this->delete($this->prefix . '/delete', ['ids' => [Arr::get($saveResult, 'data.id')]]));
});

test('getDataSourceTablePageList test', function () {
    testSuccessResponse($this->get($this->prefix . '/getDataSourceTablePageList'));
});

test('remote test', function () {
    testSuccessResponse($this->post($this->prefix . '/remote'));
});
