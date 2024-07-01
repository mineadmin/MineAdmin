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
use HyperfTests\ClientBuilder;

expect()->extend('toBeHttpSuccess', function () {
    return $this->toBeArray()
        ->toHaveKey('requestId')
        ->toHaveKey('success')
        ->toHaveKey('message')
        ->toHaveKey('code')
        ->toHaveKey('data')
        ->and($this->value['code'])
        ->toEqual(200);
});

expect()->extend('toBeHttpFail', function () {
    return $this->toBeArray()
        ->toHaveKey('requestId')
        ->toHaveKey('success')
        ->toHaveKey('message')
        ->toHaveKey('code')
        ->and($this->value['success'])
        ->toBeFalse()
        ->and($this->value['code'] !== 200)
        ->toBeTrue();
});

expect()->extend('toBeSaveAndUpdate', function (
    array $successParams,
    array $failParams,
    array $updateSuccessParams,
    array $updateFailParams,
    array $uris = ['save', 'update']
) {
    $saveUri = $this->value . '/' . $uris[0];
    foreach ($failParams as $param) {
        $failResult = ClientBuilder::post($saveUri, $param);
        expect($failResult)->toBeHttpFail();
    }
    $result = ClientBuilder::post($saveUri, $successParams);
    expect($result)->toBeHttpSuccess();
    $id = Arr::get($result, 'data.id');
    $updateUri = $this->value . '/' . $uris[1] . '/' . $id;
    expect(ClientBuilder::put($updateUri, $updateSuccessParams))->toBeHttpSuccess();
    foreach ($updateFailParams as $param) {
        expect(ClientBuilder::put($updateUri, $param))->toBeHttpFail();
    }
    return $id;
});
