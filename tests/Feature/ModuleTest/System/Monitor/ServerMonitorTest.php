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
    $this->prefix = '/system/server';
});

test('monitor test', function () {
    if (is_in_container()) {
        expect($this->get($this->prefix . '/monitor'))->toBeHttpFail();
    } else {
        expect($this->get($this->prefix . '/monitor'))->toBeHttpSuccess();
    }
});
