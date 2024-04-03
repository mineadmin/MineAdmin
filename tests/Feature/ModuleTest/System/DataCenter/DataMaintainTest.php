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
use App\System\Model\SystemUser;

beforeEach(function () {
    $this->prefix = '/system/dataMaintain';
    $this->tables = [SystemUser::getModel()->getTable()];
});

test('DataMaintain test', function () {
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'index',
        $this->buildTest('getNoParamsTest') => 'detailed',
    ]);
});

test('optimize tables test', function () {
    expect($this->post($this->prefix . '/optimize', [
        'tables' => $this->tables,
    ]))->toBeHttpSuccess();
});

test('fragment tables test', function () {
    expect($this->post($this->prefix . '/fragment', [
        'tables' => $this->tables,
    ]))->toBeHttpSuccess();
});
