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
    $this->prefix = '/system/post';
});

test('index test', function () {
    testSuccessResponse($this->get($this->prefix . '/index'));
});

test('recycle test', function () {
    testSuccessResponse($this->get($this->prefix . '/recycle'));
});

test('get test', function () {
    testSuccessResponse($this->get($this->prefix . '/list'));
    testSuccessResponse($this->post($this->prefix . '/remote'));
});

test('save test', function () {
    testFailResponse($this->post($this->prefix . '/save', []));
    testFailResponse($this->post($this->prefix . '/save', [
        'name' => 'xxx',
    ]));
    testFailResponse($this->post($this->prefix . '/save', [
        'code' => 'xxx',
    ]));
    testSuccessResponse($result = $this->post($this->prefix . '/save', [
        'code' => 'xxx',
        'name' => 'xxx',
    ]));

    $id = Arr::get($result, 'data.id');
    testSuccessResponse($this->get($this->prefix . '/read/' . $id));
    testSuccessResponse($this->put($this->prefix . '/update/' . $id, [
        'code' => Str::random(5),
        'name' => Str::random(5),
    ]));
    testSuccessResponse($this->delete($this->prefix . '/delete', [
        'ids' => [$id],
    ]));
    testSuccessResponse($this->put($this->prefix . '/recovery', [
        'ids' => [$id],
    ]));
    testSuccessResponse($this->put($this->prefix . '/changeStatus', [
        'id' => $id,
        'status' => 2,
    ]));
    testSuccessResponse($this->put($this->prefix . '/numberOperation', [
        'id' => $id,
        'numberName' => 'status',
    ]));
    testSuccessResponse($this->delete($this->prefix . '/realDelete', [
        'ids' => [$id],
    ]));
});
