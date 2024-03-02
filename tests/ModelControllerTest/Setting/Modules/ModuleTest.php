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

/**
 * This file is part of MineAdmin.
 *
 * @see     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */
beforeEach(function () {
    $this->prefix = '/setting/module';
});

test('module controller test', function () {
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'index',
    ]);
    FileSystem::delete(BASE_PATH . '/app/Demo');
    testSuccessResponse($this->put($this->prefix . '/save', [
        'name' => 'Demo',
        'label' => 'sample module',
        'version' => '1.0.0',
        'description' => 'An example of a basic module',
    ]));
    testSuccessResponse($this->put($this->prefix . '/install', [
        'name' => 'Demo',
    ]));
    testSuccessResponse($this->delete($this->prefix . '/delete', [
        'name' => 'Demo',
    ]));
});
