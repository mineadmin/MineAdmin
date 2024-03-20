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
use Nette\Utils\FileSystem;

beforeEach(function () {
    if (file_exists(BASE_PATH . '/app/Demo')) {
        FileSystem::delete(BASE_PATH . '/app/Demo');
    }
});

test('common test', function () {
    $result = $this->get('/setting/common/getModuleList');
    testSuccessResponse($result);
    expect($result['data'])
        ->toHaveCount(2);
});
