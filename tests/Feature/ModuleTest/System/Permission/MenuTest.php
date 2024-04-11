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
use App\System\Model\SystemMenu;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;

beforeEach(function () {
    $this->prefix = '/system/menu';
});
it('menu controller test', function () {
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'recycle',
        $this->buildTest('getNoParamsTest') => 'index',
        $this->buildTest('getNoParamsTest') => 'tree',
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
    $id = SystemMenu::query()->value('id');
    $this->changeStatusTest($id);
    $this->recoveryAndDeleteTest([$id]);
});
