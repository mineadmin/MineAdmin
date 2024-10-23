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
use App\System\Model\SystemRole;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;

beforeEach(function () {
    $this->prefix = '/system/role';
});

it('role controller test', function () {
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'recycle',
        $this->buildTest('getNoParamsTest') => 'index',
        $this->buildTest('getNoParamsTest') => 'list',
    ]);
    $this->remoteTest();
    $successParam = [
        'name' => Str::random(5),
        'code' => Str::random(6),
        'type' => 'B',
    ];
    $failParams = [
        Arr::only($successParam, 'name'),
        Arr::only($successParam, 'code'),
    ];
    $updateSuccessParam = [
        'name' => Str::random(5),
        'code' => Str::random(6),
        'type' => 'B',
    ];
    $updateFailParams = [
        Arr::only($updateSuccessParam, 'name'),
        Arr::only($updateSuccessParam, 'code'),
    ];
    expect($this->prefix)->toBeSaveAndUpdate($successParam, $failParams, $updateSuccessParam, $updateFailParams);
    $id = SystemRole::query()->value('id');
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'getDeptByRole/' . $id,
        $this->buildTest('getNoParamsTest') => 'getMenuByRole/' . $id,
    ]);
    $this->put($this->prefix . '/menuPermission/' . $id, [
        'code' => Str::random(6),
    ]);
    $this->put($this->prefix . '/dataPermission/' . $id, [
        'code' => Str::random(6),
    ]);

    $this->changeStatusTest($id);
    $this->recoveryAndDeleteTest([$id]);
});
