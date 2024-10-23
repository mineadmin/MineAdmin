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
use App\System\Model\SystemApi;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;

beforeEach(function () {
    $this->prefix = '/system/api';
});

test('Api Group get test', function () {
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'index',
        $this->buildTest('getNoParamsTest') => 'recycle',
        $this->buildTest('getNoParamsTest') => 'getModuleList',
    ]);
    $this->remoteTest();
});

test('Api Group put test', function () {
    $successParam = [
        'name' => Str::random(20),
        'class_name' => Str::random(20),
        'method_name' => Str::random(20),
        'access_name' => Str::random(20),
        'auth_mode' => 1,
        'request_mode' => 1,
        'group_id' => 1,
    ];
    $failParams = [
        Arr::only($successParam, 'name'),
        Arr::only($successParam, 'class_name'),
        Arr::only($successParam, 'method_name'),
        Arr::only($successParam, 'auth_mode'),
        Arr::only($successParam, 'request_mode'),
        Arr::only($successParam, 'access_name'),
        Arr::only($successParam, 'group_id'),
    ];
    $updateSuccessParam = [
        'name' => Str::random(20),
        'class_name' => Str::random(20),
        'method_name' => Str::random(20),
        'access_name' => Str::random(20),
        'auth_mode' => 1,
        'request_mode' => 1,
        'group_id' => 1,
    ];
    $updateFailParams = [
        Arr::only($updateSuccessParam, 'name'),
        Arr::only($updateSuccessParam, 'class_name'),
        Arr::only($updateSuccessParam, 'method_name'),
        Arr::only($updateSuccessParam, 'auth_mode'),
        Arr::only($updateSuccessParam, 'request_mode'),
        Arr::only($updateSuccessParam, 'access_name'),
        Arr::only($updateSuccessParam, 'group_id'),
    ];
    expect($this->prefix)->toBeSaveAndUpdate($successParam, $failParams, $updateSuccessParam, $updateFailParams);
    $id = SystemApi::query()->orderByDesc('id')->value('id');
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'read/' . $id,
    ]);

    $this->changeStatusTest($id);
    $this->recoveryAndDeleteTest([$id]);
});
