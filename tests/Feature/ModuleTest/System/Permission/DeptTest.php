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
    $this->prefix = '/system/dept';
});

test('index test', function () {
    testSuccessResponse($this->get($this->prefix . '/index'));
});

test('recycle test', function () {
    testSuccessResponse($this->get($this->prefix . '/recycle'));
});

test('tree test', function () {
    testSuccessResponse($this->get($this->prefix . '/tree'));
});

test('getLeaderList test', function () {
    testSuccessResponse($this->get($this->prefix . '/getLeaderList', ['dept_id' => 1]));
});

test('save test', function () {
    testFailResponse($this->post($this->prefix . '/save', []));
    testFailResponse($this->post($this->prefix . '/save', [
        'name' => Str::random(32),
    ]));
    testSuccessResponse($this->post($this->prefix . '/save', [
        'name' => Str::random(15),
    ]));
});

test('addLeader test', function () {
    testFailResponse($this->post($this->prefix . '/addLeader'));
    testFailResponse($this->post($this->prefix . '/addLeader', [
        'users' => [1, 2, 3],
    ]));
    testFailResponse($this->post($this->prefix . '/addLeader', [
        'id' => 1,
    ]));
    testFailResponse($this->post($this->prefix . '/addLeader', [
        'id' => 1,
        'users' => [],
    ]));
});

test('delLeader test', function () {
    testFailResponse($this->delete($this->prefix . '/delLeader'));

    testFailResponse($this->delete($this->prefix . '/delLeader', [
        'users' => [1, 2, 3],
    ]));

    testSuccessResponse($this->delete($this->prefix . '/delLeader', [
        'id' => 1,
    ]));
});

test('update test', function () {
    $id = Arr::get($this->get($this->prefix . '/index'), 'data.0.id');
    testSuccessResponse($this->put($this->prefix . '/update/' . $id, [
        'name' => Str::random(12),
    ]));

    testSuccessResponse($this->put($this->prefix . '/changeStatus', [
        'id' => $id,
        'status' => 2,
    ]));

    testSuccessResponse($this->put($this->prefix . '/numberOperation', [
        'id' => $id,
        'numberName' => 'created_by',
    ]));
});

test('delete and real delete test', function () {
    $ids = array_column(Arr::get($this->get($this->prefix . '/index'), 'data'), 'id');
    testSuccessResponse($this->delete($this->prefix . '/delete', [
        'ids' => $ids,
    ]));
    testSuccessResponse($this->delete($this->prefix . '/realDelete', [
        'ids' => $ids,
    ]));
    testSuccessResponse($this->put($this->prefix . '/recovery', [
        'ids' => $ids,
    ]));
});

test('remote test', function () {
    testSuccessResponse($this->post($this->prefix . '/remote'));
});
