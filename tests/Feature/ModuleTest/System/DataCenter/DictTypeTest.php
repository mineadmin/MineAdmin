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
use App\System\Model\SystemDictType;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;

beforeEach(function () {
    $this->prefix = '/system/dictType';
});

test('dict type controller test', function () {
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'index',
        $this->buildTest('getNoParamsTest') => 'recycle',
    ]);
    $this->remoteTest();

    $successParam = [
        'name' => Str::random(30),
        'code' => Str::random(30),
    ];
    $failParams = [
        Arr::only($successParam, 'name'),
        Arr::only($successParam, 'code'),
    ];
    $updateSuccessParam = [
        'name' => Str::random(30),
        'code' => Str::random(30),
    ];
    $updateFailParams = [
        Arr::only($updateSuccessParam, 'name'),
        Arr::only($updateSuccessParam, 'code'),
    ];
    expect($this->prefix)->toBeSaveAndUpdate($successParam, $failParams, $updateSuccessParam, $updateFailParams);
    $id = SystemDictType::query()->where('name', $updateSuccessParam['name'])->value('id');
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'read/' . $id,
    ]);
    $this->changeStatusTest($id);
    $this->recoveryAndDeleteTest([$id]);
});
