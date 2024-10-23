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
    expect($this->get($this->prefix . '/index'))->toBeHttpSuccess();
});

test('recycle test', function () {
    expect($this->get($this->prefix . '/recycle'))->toBeHttpSuccess();
});

test('tree test', function () {
    expect($this->get($this->prefix . '/tree'))->toBeHttpSuccess();
});

test('getLeaderList test', function () {
    expect($this->get($this->prefix . '/getLeaderList', ['dept_id' => 1]))->toBeHttpSuccess();
});

test('save test', function () {
    expect($this->post($this->prefix . '/save', []))->toBeHttpFail()
        ->and($this->post($this->prefix . '/save', [
            'name' => Str::random(32),
        ]))->toBeHttpFail()
        ->and($this->post($this->prefix . '/save', [
            'name' => Str::random(15),
        ]))->toBeHttpSuccess();
});

test('addLeader test', function () {
    expect($this->post($this->prefix . '/addLeader'))->toBeHttpFail()
        ->and($this->post($this->prefix . '/addLeader', [
            'users' => [1, 2, 3],
        ]))->toBeHttpFail()
        ->and($this->post($this->prefix . '/addLeader', [
            'id' => 1,
        ]))->toBeHttpFail()
        ->and($this->post($this->prefix . '/addLeader', [
            'id' => 1,
            'users' => [],
        ]))->toBeHttpFail();
});

test('delLeader test', function () {
    expect($this->delete($this->prefix . '/delLeader'))->toBeHttpFail()
        ->and($this->delete($this->prefix . '/delLeader', [
            'users' => [1, 2, 3],
        ]))->toBeHttpFail()
        ->and($this->delete($this->prefix . '/delLeader', [
            'id' => 1,
        ]))->toBeHttpSuccess();
});

test('update test', function () {
    $id = Arr::get($this->get($this->prefix . '/index'), 'data.0.id');
    expect($this->put($this->prefix . '/update/' . $id, [
        'name' => Str::random(12),
    ]))->toBeHttpSuccess()
        ->and($this->put($this->prefix . '/changeStatus', [
            'id' => $id,
            'status' => 2,
        ]))->toBeHttpSuccess()
        ->and($this->put($this->prefix . '/numberOperation', [
            'id' => $id,
            'numberName' => 'created_by',
        ]))->toBeHttpSuccess();
});

test('delete and real delete test', function () {
    $ids = array_column(Arr::get($this->get($this->prefix . '/index'), 'data'), 'id');
    expect($this->delete($this->prefix . '/delete', [
        'ids' => $ids,
    ]))->toBeHttpSuccess()
        ->and($this->delete($this->prefix . '/realDelete', [
            'ids' => $ids,
        ]))->toBeHttpSuccess()
        ->and($this->put($this->prefix . '/recovery', [
            'ids' => $ids,
        ]))->toBeHttpSuccess();
});

test('remote test', function () {
    expect($this->post($this->prefix . '/remote'))->toBeHttpSuccess();
});
