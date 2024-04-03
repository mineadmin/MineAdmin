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
use App\System\Model\SystemApp;
use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;

beforeEach(function () {
    $this->prefix = '/system/app';
});

test('App get test', function () {
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'getAppId',
        $this->buildTest('getNoParamsTest') => 'getAppSecret',
        $this->buildTest('getNoParamsTest') => 'index',
        $this->buildTest('getNoParamsTest') => 'getApiList',
        $this->buildTest('getNoParamsTest') => 'recycle',
    ]);
    $this->remoteTest();
});

test('App Group put test', function () {
    $successParam = [
        'app_name' => Str::random(5),
        'group_id' => 1,
        'app_id' => 1,
        'app_secret' => Str::random(6),
    ];
    $failParams = [
        Arr::only($successParam, 'app_name'),
        Arr::only($successParam, 'group_id'),
        Arr::only($successParam, 'app_secret'),
        Arr::only($successParam, 'app_id'),
    ];
    $updateSuccessParam = [
        'app_name' => Str::random(5),
        'group_id' => 1,
        'app_id' => 1,
        'app_secret' => Str::random(6),
    ];
    $updateFailParams = [
        Arr::only($updateSuccessParam, 'app_name'),
        Arr::only($updateSuccessParam, 'group_id'),
        Arr::only($updateSuccessParam, 'app_secret'),
        Arr::only($updateSuccessParam, 'app_id'),
    ];
    expect($this->prefix)->toBeSaveAndUpdate($successParam, $failParams, $updateSuccessParam, $updateFailParams);
    $id = SystemApp::query()->value('id');
    $this->actionTest([
        $this->buildTest('getNoParamsTest') => 'read/' . $id,
    ]);

    expect($this->put($this->prefix . '/bind/' . $id))->toBeHttpSuccess();
    $this->recoveryAndDeleteTest([$id]);
});
