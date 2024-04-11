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

test('common test', function () {
    FileSystem::delete(BASE_PATH . '/app/Demo');
    $result = $this->get('/setting/common/getModuleList');
    expect($result)->toBeHttpSuccess()
        ->and($result['data'])
        ->toHaveCount(2);
});
