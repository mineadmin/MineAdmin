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
