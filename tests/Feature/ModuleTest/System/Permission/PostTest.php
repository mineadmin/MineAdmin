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
    expect($this->get($this->prefix . '/index'))->toBeHttpSuccess();
});

test('recycle test', function () {
    expect($this->get($this->prefix . '/recycle'))->toBeHttpSuccess();
});

test('get test', function () {
    expect($this->get($this->prefix . '/list'))->toBeHttpSuccess()
        ->and($this->post($this->prefix . '/remote'))->toBeHttpSuccess();
});

test('save test', function () {
    expect($this->post($this->prefix . '/save', []))->toBeHttpFail()
        ->and($this->post($this->prefix . '/save', [
            'name' => 'xxx',
        ]))->toBeHttpFail()
        ->and($this->post($this->prefix . '/save', [
            'code' => 'xxx',
        ]))->toBeHttpFail()
        ->and($result = $this->post($this->prefix . '/save', [
            'code' => 'xxx',
            'name' => 'xxx',
        ]))->toBeHttpSuccess();

    $id = Arr::get($result, 'data.id');
    expect($this->get($this->prefix . '/read/' . $id))->toBeHttpSuccess()
        ->and($this->put($this->prefix . '/update/' . $id, [
            'code' => Str::random(5),
            'name' => Str::random(5),
        ]))->toBeHttpSuccess()
        ->and($this->delete($this->prefix . '/delete', [
            'ids' => [$id],
        ]))->toBeHttpSuccess()
        ->and($this->put($this->prefix . '/recovery', [
            'ids' => [$id],
        ]))->toBeHttpSuccess()
        ->and($this->put($this->prefix . '/changeStatus', [
            'id' => $id,
            'status' => 2,
        ]))->toBeHttpSuccess()
        ->and($this->put($this->prefix . '/numberOperation', [
            'id' => $id,
            'numberName' => 'status',
        ]))->toBeHttpSuccess()
        ->and($this->delete($this->prefix . '/realDelete', [
            'ids' => [$id],
        ]))->toBeHttpSuccess();
});
